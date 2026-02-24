<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\XpTransaction;
use App\Services\XpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'all');
        $xpService = app(XpService::class);

        if ($period === 'all') {
            // Use cached total_xp for all-time (fast)
            $users = User::where('total_xp', '>', 0)
                ->orderByDesc('total_xp')
                ->with('roles')
                ->paginate(20);
        } else {
            // For time-filtered periods, aggregate from xp_transactions
            $dateFrom = match ($period) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                default => null,
            };

            $userIds = XpTransaction::select('user_id', DB::raw('SUM(xp_amount) as period_xp'))
                ->when($dateFrom, fn ($q) => $q->where('created_at', '>=', $dateFrom))
                ->groupBy('user_id')
                ->having('period_xp', '>', 0)
                ->orderByDesc('period_xp')
                ->pluck('period_xp', 'user_id');

            $users = User::whereIn('id', $userIds->keys())
                ->with('roles')
                ->get()
                ->map(function ($user) use ($userIds) {
                    $user->period_xp = $userIds[$user->id] ?? 0;
                    return $user;
                })
                ->sortByDesc('period_xp')
                ->values();
        }

        // Current user's rank
        $myRank = null;
        $myPeriodXp = null;
        if (auth()->check()) {
            if ($period === 'all') {
                $myRank = User::where('total_xp', '>', auth()->user()->total_xp)->count() + 1;
            } else {
                $dateFrom = match ($period) {
                    'week' => now()->subWeek(),
                    'month' => now()->subMonth(),
                    default => null,
                };
                $myPeriodXp = XpTransaction::where('user_id', auth()->id())
                    ->when($dateFrom, fn ($q) => $q->where('created_at', '>=', $dateFrom))
                    ->sum('xp_amount');
                $myRank = XpTransaction::select('user_id', DB::raw('SUM(xp_amount) as period_xp'))
                    ->when($dateFrom, fn ($q) => $q->where('created_at', '>=', $dateFrom))
                    ->groupBy('user_id')
                    ->having('period_xp', '>', max(0, $myPeriodXp))
                    ->count() + 1;
            }
        }

        return view('leaderboard.index', compact('users', 'period', 'myRank', 'myPeriodXp'));
    }
}
