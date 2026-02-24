<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * Get a friendly display name for the auditable model type.
     */
    public function getModelDisplayNameAttribute(): string
    {
        if (!$this->auditable_type) {
            return 'System';
        }

        return class_basename($this->auditable_type);
    }

    /**
     * Get color for the action badge.
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created' => 'emerald',
            'updated' => 'amber',
            'deleted' => 'red',
            'login' => 'blue',
            'logout' => 'gray',
            'roles_synced' => 'purple',
            'role_assigned' => 'purple',
            'role_removed' => 'purple',
            'bulk_check' => 'blue',
            'xp_revoked' => 'red',
            'xp_backfill' => 'amber',
            'level_up' => 'emerald',
            'webhook_test' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Create an audit log entry with request context automatically filled.
     */
    public static function log(
        string $action,
        ?string $description = null,
        ?Model $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null,
    ): static {
        return static::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->getKey(),
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
