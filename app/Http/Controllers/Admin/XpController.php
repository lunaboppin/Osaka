<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\XpTransaction;
use App\Services\DiscordWebhookService;
use App\Services\XpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class XpController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->where('total_xp', '>', 0);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderByDesc('total_xp')->paginate(20)->withQueryString();

        return view('admin.xp.index', compact('users'));
    }

    public function show(User $user)
    {
        $transactions = $user->xpTransactions()
            ->latest()
            ->paginate(25);

        return view('admin.xp.show', compact('user', 'transactions'));
    }

    public function revoke(Request $request, User $user)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1|max:' . $user->total_xp,
            'reason' => 'required|string|max:500',
        ]);

        $amount = $validated['amount'];
        $reason = $validated['reason'];

        // Create a negative XP transaction
        XpTransaction::create([
            'user_id' => $user->id,
            'action' => 'xp_revoked',
            'xp_amount' => -$amount,
            'description' => "XP revoked: {$reason}",
            'metadata' => [
                'revoked_by' => Auth::id(),
                'reason' => $reason,
            ],
        ]);

        // Atomically deduct from total (floor at 0)
        $decrement = min($amount, $user->total_xp);
        if ($decrement > 0) {
            $user->decrement('total_xp', $decrement);
        }
        $user->refresh();

        // Audit log
        AuditLog::log(
            'xp_revoked',
            "Revoked {$amount} XP from {$user->name}: {$reason}",
            $user,
            ['total_xp' => $user->total_xp + $amount],
            ['total_xp' => $user->total_xp],
        );

        // Discord notification
        app(DiscordWebhookService::class)->notifyXpRevoked(
            $user,
            Auth::user(),
            $amount,
            $reason,
        );

        return back()->with('success', "Revoked {$amount} XP from {$user->name}.");
    }
}
