<?php

namespace Database\Seeders;

use App\Models\StickerType;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        // Ensure a default sticker type exists
        StickerType::firstOrCreate(
            ['name' => 'stickers'],
            [
                'display_name' => 'Stickers',
                'description' => 'Default sticker type',
                'color' => '#D97706',
            ]
        );
    }
}
