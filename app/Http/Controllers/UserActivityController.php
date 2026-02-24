<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\XpTransaction;
use App\Services\XpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserActivityController extends Controller
{
    public function show(User $user)
    {
        $xpService = app(XpService::class);

        // XP transactions (paginated, latest first)
        $transactions = $user->xpTransactions()
            ->latest()
            ->paginate(20);

        // Action breakdown
        $actionBreakdown = $user->xpTransactions()
            ->select('action', DB::raw('COUNT(*) as count'), DB::raw('SUM(xp_amount) as total_xp'))
            ->groupBy('action')
            ->orderByDesc('total_xp')
            ->get();

        // Activity stats
        $stats = [
            'total_xp' => $user->total_xp,
            'level' => $user->level,
            'level_name' => $user->level_name,
            'level_progress' => $user->level_progress,
            'xp_for_next_level' => $user->xp_for_next_level,
            'current_level_threshold' => $user->current_level_threshold,
            'next_level_threshold' => $user->next_level_threshold,
            'total_pins' => $user->pins()->count(),
            'total_updates' => \App\Models\PinUpdate::where('user_id', $user->id)
                ->where('notes', '!=', 'Initial pin creation.')
                ->where('notes', '!=', 'Marked as checked — no changes.')
                ->count(),
            'total_checks' => \App\Models\PinUpdate::where('user_id', $user->id)
                ->where('notes', 'Marked as checked — no changes.')
                ->count(),
            'total_photos' => $user->xpTransactions()->where('action', 'photo_added')->count(),
            'days_active' => $user->xpTransactions()
                ->select(DB::raw('DATE(created_at) as active_date'))
                ->distinct()
                ->count(),
        ];

        // Current streak (consecutive days with XP activity ending today or yesterday)
        $activeDates = $user->xpTransactions()
            ->select(DB::raw('DATE(created_at) as active_date'))
            ->distinct()
            ->orderByDesc('active_date')
            ->pluck('active_date')
            ->map(fn ($d) => \Carbon\Carbon::parse($d)->startOfDay());

        $streak = 0;
        $checkDate = now()->startOfDay();

        // Allow streak to start from today or yesterday
        if ($activeDates->isNotEmpty()) {
            $first = $activeDates->first();
            if ($first->eq($checkDate) || $first->eq($checkDate->copy()->subDay())) {
                $checkDate = $first->copy();
                foreach ($activeDates as $date) {
                    if ($date->eq($checkDate)) {
                        $streak++;
                        $checkDate = $checkDate->subDay();
                    } else {
                        break;
                    }
                }
            }
        }
        $stats['current_streak'] = $streak;

        // Milestones: when each level was reached
        $milestones = [];
        $thresholds = config('osaka.xp.thresholds', [0]);
        $names = config('osaka.xp.names', ['Newcomer']);
        $runningXp = 0;
        $levelReachedDates = [];

        $allTransactions = $user->xpTransactions()->orderBy('created_at')->get();
        foreach ($allTransactions as $txn) {
            $oldLevel = $xpService->getLevel($runningXp);
            $runningXp = max(0, $runningXp + $txn->xp_amount);
            $newLevel = $xpService->getLevel($runningXp);

            if ($newLevel > $oldLevel) {
                for ($l = $oldLevel + 1; $l <= $newLevel; $l++) {
                    if (!isset($levelReachedDates[$l])) {
                        $levelReachedDates[$l] = $txn->created_at;
                    }
                }
            }
        }

        foreach ($levelReachedDates as $level => $date) {
            $milestones[] = [
                'level' => $level,
                'name' => $names[$level - 1] ?? 'Unknown',
                'reached_at' => $date,
                'xp_required' => $thresholds[$level - 1] ?? 0,
            ];
        }
        $stats['milestones'] = $milestones;

        return view('users.activity', compact('user', 'transactions', 'actionBreakdown', 'stats'));
    }
}
