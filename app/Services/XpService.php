<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\XpTransaction;
use App\Services\DiscordWebhookService;
use Illuminate\Database\Eloquent\Model;

class XpService
{
    /**
     * Award XP for a given action.
     *
     * Returns the created transaction, or null if the action has no XP value.
     */
    public function award(User $user, string $action, ?string $description = null, ?Model $actionable = null, ?array $metadata = null): ?XpTransaction
    {
        $amount = config("osaka.xp.amounts.{$action}");

        if ($amount === null || $amount === 0) {
            return null;
        }

        $transaction = XpTransaction::create([
            'user_id' => $user->id,
            'action' => $action,
            'xp_amount' => $amount,
            'description' => $description,
            'xp_actionable_type' => $actionable ? get_class($actionable) : null,
            'xp_actionable_id' => $actionable?->id,
            'metadata' => $metadata,
        ]);

        $oldLevel = $this->getLevel($user->total_xp);

        // Atomically update cached total (floor at 0)
        if ($amount > 0) {
            $user->increment('total_xp', $amount);
        } else {
            $decrement = min(abs($amount), $user->total_xp);
            if ($decrement > 0) {
                $user->decrement('total_xp', $decrement);
            }
        }

        $user->refresh();
        $newLevel = $this->getLevel($user->total_xp);

        // Log level-up + Discord notification
        if ($newLevel > $oldLevel) {
            AuditLog::log(
                'level_up',
                "Reached Level {$newLevel} — {$this->getLevelName($newLevel)}",
                $user,
            );

            app(DiscordWebhookService::class)->notifyLevelUp(
                $user,
                $newLevel,
                $this->getLevelName($newLevel),
            );
        }

        return $transaction;
    }

    /**
     * Deduct XP (convenience wrapper for negative actions).
     */
    public function deduct(User $user, string $action, ?string $description = null, ?Model $actionable = null): ?XpTransaction
    {
        return $this->award($user, $action, $description, $actionable);
    }

    /**
     * Compute the level for a given XP total.
     */
    public function getLevel(int $xp): int
    {
        $thresholds = config('osaka.xp.thresholds', [0]);

        $level = 1;
        foreach ($thresholds as $index => $threshold) {
            if ($xp >= $threshold) {
                $level = $index + 1;
            } else {
                break;
            }
        }

        return $level;
    }

    /**
     * Get the named rank for a level.
     */
    public function getLevelName(int $level): string
    {
        $names = config('osaka.xp.names', ['Newcomer']);

        return $names[$level - 1] ?? $names[count($names) - 1] ?? 'Unknown';
    }

    /**
     * XP remaining until the next level, or null if at max.
     */
    public function getXpForNextLevel(int $xp): ?int
    {
        $thresholds = config('osaka.xp.thresholds', [0]);
        $level = $this->getLevel($xp);

        if ($level >= count($thresholds)) {
            return null; // max level
        }

        return $thresholds[$level] - $xp;
    }

    /**
     * Progress percentage (0.0–1.0) towards the next level.
     */
    public function getLevelProgress(int $xp): float
    {
        $thresholds = config('osaka.xp.thresholds', [0]);
        $level = $this->getLevel($xp);

        if ($level >= count($thresholds)) {
            return 1.0; // max level
        }

        $currentThreshold = $thresholds[$level - 1];
        $nextThreshold = $thresholds[$level];
        $range = $nextThreshold - $currentThreshold;

        if ($range <= 0) {
            return 1.0;
        }

        return ($xp - $currentThreshold) / $range;
    }

    /**
     * Get the XP threshold for the current level.
     */
    public function getCurrentLevelThreshold(int $xp): int
    {
        $thresholds = config('osaka.xp.thresholds', [0]);
        $level = $this->getLevel($xp);

        return $thresholds[$level - 1] ?? 0;
    }

    /**
     * Get the XP threshold for the next level, or null if max.
     */
    public function getNextLevelThreshold(int $xp): ?int
    {
        $thresholds = config('osaka.xp.thresholds', [0]);
        $level = $this->getLevel($xp);

        if ($level >= count($thresholds)) {
            return null;
        }

        return $thresholds[$level];
    }

    /**
     * Retroactively award XP for a user's existing pins and updates.
     * Guarded by xp_backfilled_at to prevent duplicates.
     *
     * Returns total XP awarded.
     */
    public function backfillUser(User $user): int
    {
        if ($user->xp_backfilled_at) {
            return 0;
        }

        $totalAwarded = 0;
        $meta = ['backfilled' => true];

        // Award XP for each pin the user created
        foreach ($user->pins as $pin) {
            $amount = config('osaka.xp.amounts.pin_created', 10);
            XpTransaction::create([
                'user_id' => $user->id,
                'action' => 'pin_created',
                'xp_amount' => $amount,
                'description' => "Created pin: {$pin->title}",
                'xp_actionable_type' => get_class($pin),
                'xp_actionable_id' => $pin->id,
                'metadata' => $meta,
            ]);
            $totalAwarded += $amount;

            // Bonus for photo on creation
            if ($pin->photo) {
                $photoAmount = config('osaka.xp.amounts.photo_added', 3);
                XpTransaction::create([
                    'user_id' => $user->id,
                    'action' => 'photo_added',
                    'xp_amount' => $photoAmount,
                    'description' => "Added photo to pin: {$pin->title}",
                    'xp_actionable_type' => get_class($pin),
                    'xp_actionable_id' => $pin->id,
                    'metadata' => $meta,
                ]);
                $totalAwarded += $photoAmount;
            }
        }

        // Award XP for each pin update the user posted (excluding the initial creation entry)
        $updates = \App\Models\PinUpdate::where('user_id', $user->id)
            ->where('notes', '!=', 'Initial pin creation.')
            ->with('pin')
            ->get();

        foreach ($updates as $update) {
            $pinTitle = $update->pin?->title ?? 'Unknown pin';

            if ($update->notes === 'Marked as checked — no changes.') {
                // This was a check action
                $amount = config('osaka.xp.amounts.pin_checked', 2);
                XpTransaction::create([
                    'user_id' => $user->id,
                    'action' => 'pin_checked',
                    'xp_amount' => $amount,
                    'description' => "Checked pin: {$pinTitle}",
                    'xp_actionable_type' => get_class($update),
                    'xp_actionable_id' => $update->id,
                    'metadata' => $meta,
                ]);
                $totalAwarded += $amount;
            } else {
                // Regular timeline update
                $amount = config('osaka.xp.amounts.update_posted', 5);
                XpTransaction::create([
                    'user_id' => $user->id,
                    'action' => 'update_posted',
                    'xp_amount' => $amount,
                    'description' => "Posted update on pin: {$pinTitle}",
                    'xp_actionable_type' => get_class($update),
                    'xp_actionable_id' => $update->id,
                    'metadata' => $meta,
                ]);
                $totalAwarded += $amount;

                // Bonus for photo on update
                if ($update->photo) {
                    $photoAmount = config('osaka.xp.amounts.photo_added', 3);
                    XpTransaction::create([
                        'user_id' => $user->id,
                        'action' => 'photo_added',
                        'xp_amount' => $photoAmount,
                        'description' => "Added photo to update on pin: {$pinTitle}",
                        'xp_actionable_type' => get_class($update),
                        'xp_actionable_id' => $update->id,
                        'metadata' => $meta,
                    ]);
                    $totalAwarded += $photoAmount;
                }
            }
        }

        // Profile completion bonus
        if ($user->bio && $user->avatar) {
            $amount = config('osaka.xp.amounts.profile_completed', 1);
            XpTransaction::create([
                'user_id' => $user->id,
                'action' => 'profile_completed',
                'xp_amount' => $amount,
                'description' => 'Profile completed (bio & avatar set)',
                'metadata' => $meta,
            ]);
            $totalAwarded += $amount;
        }

        // Set cached total and mark as backfilled
        $user->update([
            'total_xp' => $totalAwarded,
            'xp_backfilled_at' => now(),
        ]);

        if ($totalAwarded > 0) {
            AuditLog::log(
                'xp_backfill',
                "Retroactive XP awarded: {$totalAwarded} XP",
                $user,
            );
        }

        return $totalAwarded;
    }
}
