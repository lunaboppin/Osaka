<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pins', function (Blueprint $table) {
            $table->enum('status', ['New', 'Worn', 'Needs replaced', 'Missing'])->default('New')->change();
        });
        Schema::table('pin_updates', function (Blueprint $table) {
            $table->enum('status', ['New', 'Worn', 'Needs replaced', 'Missing'])->default('New')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pins', function (Blueprint $table) {
            $table->enum('status', ['New', 'Worn', 'Needs replaced'])->default('New')->change();
        });
        Schema::table('pin_updates', function (Blueprint $table) {
            $table->enum('status', ['New', 'Worn', 'Needs replaced'])->default('New')->change();
        });
    }
};
