<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin'], [
            'display_name' => 'Admin',
            'color' => '#C41E3A',
            'priority' => 100,
            'permissions' => ['*'],
        ]);

        Role::firstOrCreate(['name' => 'moderator'], [
            'display_name' => 'Moderator',
            'color' => '#D4A843',
            'priority' => 50,
            'permissions' => [
                'pins.create',
                'pins.edit_own',
                'pins.edit_any',
                'pins.delete_own',
                'pins.check',
                'updates.create',
                'updates.delete_own',
                'updates.delete_any',
                'users.view_profiles',
            ],
        ]);

        Role::firstOrCreate(['name' => 'member'], [
            'display_name' => 'Member',
            'color' => '#6B7280',
            'priority' => 10,
            'permissions' => [
                'pins.create',
                'pins.edit_own',
                'pins.delete_own',
                'pins.check',
                'updates.create',
                'updates.delete_own',
                'users.view_profiles',
            ],
        ]);
    }
}
