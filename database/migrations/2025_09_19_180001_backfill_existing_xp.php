<?php

use App\Models\User;
use App\Services\XpService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $xpService = new XpService();

        User::whereNull('xp_backfilled_at')->each(function (User $user) use ($xpService) {
            $xpService->backfillUser($user);
        });
    }

    public function down(): void
    {
        // Remove all backfilled transactions and reset totals
        \App\Models\XpTransaction::whereJsonContains('metadata->backfilled', true)->delete();

        User::whereNotNull('xp_backfilled_at')->update([
            'total_xp' => 0,
            'xp_backfilled_at' => null,
        ]);
    }
};
