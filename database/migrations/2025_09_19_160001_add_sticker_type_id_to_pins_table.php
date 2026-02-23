<?php

use App\Models\StickerType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create a default sticker type
        $default = StickerType::firstOrCreate(
            ['name' => 'stickers'],
            [
                'display_name' => 'Stickers',
                'description' => 'Default sticker type',
                'color' => '#D97706',
            ]
        );

        Schema::table('pins', function (Blueprint $table) {
            $table->foreignId('sticker_type_id')
                ->nullable()
                ->after('user_id')
                ->constrained('sticker_types')
                ->nullOnDelete();
        });

        // Assign all existing pins to the default sticker type
        \App\Models\Pin::whereNull('sticker_type_id')->update([
            'sticker_type_id' => $default->id,
        ]);
    }

    public function down(): void
    {
        Schema::table('pins', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sticker_type_id');
        });
    }
};
