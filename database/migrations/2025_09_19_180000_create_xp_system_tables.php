<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('xp_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action');               // e.g. pin_created, update_posted, photo_added
            $table->integer('xp_amount');            // can be negative for deductions
            $table->string('description')->nullable();
            $table->nullableMorphs('xp_actionable'); // related model (Pin, PinUpdate, etc.)
            $table->json('metadata')->nullable();     // extra context like {backfilled: true}
            $table->timestamps();

            $table->index(['user_id', 'action']);
            $table->index(['user_id', 'created_at']);
        });

        if (!Schema::hasColumn('users', 'total_xp')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('total_xp')->default(0)->after('default_sticker_type_id');
                $table->timestamp('xp_backfilled_at')->nullable()->after('total_xp');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('xp_transactions');

        if (Schema::hasColumn('users', 'total_xp')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['total_xp', 'xp_backfilled_at']);
            });
        }
    }
};
