<?php

namespace App\Services;

use App\Models\DiscordWebhook;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordWebhookService
{
    /**
     * Send a Discord embed to all webhooks listening for a given event.
     */
    public function send(string $event, array $embed): void
    {
        $webhooks = DiscordWebhook::forEvent($event)->get();

        foreach ($webhooks as $webhook) {
            $this->dispatch($webhook, $embed);
        }
    }

    /**
     * Dispatch a payload to a single webhook.
     */
    protected function dispatch(DiscordWebhook $webhook, array $embed): void
    {
        try {
            $payload = [
                'embeds' => [$embed],
            ];

            Http::timeout(5)->post($webhook->url, $payload);

            $webhook->update(['last_used_at' => now()]);
        } catch (\Throwable $e) {
            Log::warning("Discord webhook [{$webhook->name}] failed: {$e->getMessage()}");
        }
    }

    /**
     * Send a test message to verify a webhook URL works.
     */
    public function test(DiscordWebhook $webhook): bool
    {
        try {
            $response = Http::timeout(5)->post($webhook->url, [
                'embeds' => [[
                    'title' => '🏯 Osaka Webhook Test',
                    'description' => "This webhook (**{$webhook->name}**) is connected and working!",
                    'color' => 0xD4A843, // osaka-gold
                    'footer' => ['text' => 'Osaka • Webhook Test'],
                    'timestamp' => now()->toIso8601String(),
                ]],
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning("Discord webhook test [{$webhook->name}] failed: {$e->getMessage()}");
            return false;
        }
    }

    // ── Pre-built event embeds ──────────────────────────────

    public function notifyPinCreated(\App\Models\Pin $pin, \App\Models\User $user): void
    {
        $this->send('pin_created', [
            'title' => '📌 New Pin Created',
            'description' => "**{$pin->title}**" . ($pin->description ? "\n{$pin->description}" : ''),
            'color' => 0x10B981, // emerald
            'fields' => [
                ['name' => 'Created by', 'value' => $user->name, 'inline' => true],
            ],
            'footer' => ['text' => 'Osaka • Pin Tracker'],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function notifyPinDeleted(\App\Models\Pin $pin, \App\Models\User $user): void
    {
        $this->send('pin_deleted', [
            'title' => '🗑️ Pin Deleted',
            'description' => "**{$pin->title}** was deleted.",
            'color' => 0xEF4444, // red
            'fields' => [
                ['name' => 'Deleted by', 'value' => $user->name, 'inline' => true],
            ],
            'footer' => ['text' => 'Osaka • Pin Tracker'],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function notifyUpdatePosted(\App\Models\PinUpdate $update, \App\Models\Pin $pin, \App\Models\User $user): void
    {
        $this->send('update_posted', [
            'title' => '📝 Timeline Update',
            'description' => "New update on **{$pin->title}**" . ($update->notes ? "\n> {$update->notes}" : ''),
            'color' => 0xF59E0B, // amber
            'fields' => [
                ['name' => 'Posted by', 'value' => $user->name, 'inline' => true],
                ['name' => 'Status', 'value' => ucfirst($update->status ?? 'update'), 'inline' => true],
            ],
            'footer' => ['text' => 'Osaka • Pin Tracker'],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function notifyLevelUp(\App\Models\User $user, int $newLevel, string $levelName): void
    {
        $this->send('user_level_up', [
            'title' => '🎉 Level Up!',
            'description' => "**{$user->name}** reached **Level {$newLevel}** — *{$levelName}*!",
            'color' => 0xD4A843, // osaka-gold
            'footer' => ['text' => 'Osaka • XP System'],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function notifyXpRevoked(\App\Models\User $targetUser, \App\Models\User $admin, int $amount, string $reason): void
    {
        $this->send('xp_revoked', [
            'title' => '⚠️ XP Revoked',
            'description' => "**{$amount} XP** revoked from **{$targetUser->name}**",
            'color' => 0xEF4444, // red
            'fields' => [
                ['name' => 'Reason', 'value' => $reason, 'inline' => false],
                ['name' => 'Revoked by', 'value' => $admin->name, 'inline' => true],
                ['name' => 'New Total', 'value' => "{$targetUser->total_xp} XP", 'inline' => true],
            ],
            'footer' => ['text' => 'Osaka • XP System'],
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
