<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class DiscordWebhook extends Model
{
    use Auditable;

    protected $fillable = [
        'name',
        'url',
        'events',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /**
     * All available webhook event types.
     */
    public static function availableEvents(): array
    {
        return [
            'pin_created' => 'Pin Created',
            'pin_deleted' => 'Pin Deleted',
            'update_posted' => 'Timeline Update Posted',
            'user_level_up' => 'User Level Up',
            'xp_revoked' => 'XP Revoked',
        ];
    }

    /**
     * Check if this webhook listens for a given event.
     */
    public function listensTo(string $event): bool
    {
        $events = $this->events ?? [];

        return in_array('*', $events) || in_array($event, $events);
    }

    /**
     * Scope to only active webhooks.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to webhooks that listen for a specific event.
     */
    public function scopeForEvent($query, string $event)
    {
        return $query->active()->where(function ($q) use ($event) {
            $q->whereJsonContains('events', $event)
              ->orWhereJsonContains('events', '*');
        });
    }
}
