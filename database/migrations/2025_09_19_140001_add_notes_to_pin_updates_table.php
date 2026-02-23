<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pin_updates', function (Blueprint $table) {
            if (!Schema::hasColumn('pin_updates', 'notes')) {
                $table->text('notes')->nullable()->after('photo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pin_updates', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
