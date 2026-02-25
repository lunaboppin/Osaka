<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('banner_path')->nullable()->after('avatar');
            $table->string('accent_color', 7)->nullable()->after('banner_path');
            $table->string('profile_theme', 30)->default('default')->after('accent_color');
            $table->string('avatar_frame', 30)->nullable()->after('profile_theme');
            $table->json('displayed_badges')->nullable()->after('avatar_frame');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['banner_path', 'accent_color', 'profile_theme', 'avatar_frame', 'displayed_badges']);
        });
    }
};
