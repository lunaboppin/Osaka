<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name', 'display_name', 'color', 'permissions', 'priority',
    ];

    protected $casts = [
        'permissions' => 'array',
        'priority' => 'integer',
    ];

    /**
     * All available permissions in the system.
     */
    public static function availablePermissions(): array
    {
        return [
            'pins.create' => 'Create pins',
            'pins.edit_own' => 'Edit own pins',
            'pins.edit_any' => 'Edit any pin',
            'pins.delete_own' => 'Delete own pins',
            'pins.delete_any' => 'Delete any pin',
            'pins.check' => 'Mark pins as checked',
            'updates.create' => 'Post timeline updates',
            'updates.delete_own' => 'Delete own timeline updates',
            'updates.delete_any' => 'Delete any timeline update',
            'users.view_profiles' => 'View user profiles',
            'roles.manage' => 'Manage roles',
            'users.manage' => 'Manage users & assign roles',
            'admin.access' => 'Access admin panel',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Check if this role has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        $perms = $this->permissions ?? [];

        // Wildcard: 'admin.*' matches 'admin.access', 'admin.anything'
        foreach ($perms as $perm) {
            if ($perm === '*') return true;
            if ($perm === $permission) return true;
            if (str_ends_with($perm, '.*')) {
                $prefix = substr($perm, 0, -2);
                if (str_starts_with($permission, $prefix . '.')) return true;
            }
        }

        return false;
    }
}
