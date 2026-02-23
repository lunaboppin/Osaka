<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pin extends Model
{
    protected $fillable = [
        'title', 'description', 'latitude', 'longitude', 'status', 'photo', 'user_id', 'sticker_type_id', 'last_checked_at'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'last_checked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stickerType()
    {
        return $this->belongsTo(StickerType::class);
    }

    /**
     * Scope: filter pins by sticker type. Null = show all.
     */
    public function scopeForStickerType($query, $stickerTypeId)
    {
        if ($stickerTypeId) {
            return $query->where('sticker_type_id', $stickerTypeId);
        }

        return $query;
    }

    public function updates()
    {
        return $this->hasMany(PinUpdate::class)->latest();
    }

    public function latestUpdate()
    {
        return $this->hasOne(PinUpdate::class)->latestOfMany();
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'New' => 'green',
            'Worn' => 'amber',
            'Needs replaced' => 'red',
            default => 'gray',
        };
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    // ----- Aging / Reminder helpers -----

    /**
     * The date to measure age from: last_checked_at if set, otherwise created_at.
     */
    public function getAgeReferenceAttribute(): Carbon
    {
        return $this->last_checked_at ?? $this->created_at;
    }

    /**
     * Number of days since the pin was last checked (or created).
     */
    public function getDaysSinceCheckedAttribute(): int
    {
        return (int) $this->age_reference->diffInDays(now());
    }

    /**
     * Urgency tier: 'overdue', 'warning', or 'ok'.
     */
    public function getUrgencyAttribute(): string
    {
        $overdue = config('osaka.reminders.overdue_days', 30);
        $warning = config('osaka.reminders.warning_days', 20);

        if ($this->days_since_checked >= $overdue) {
            return 'overdue';
        }

        if ($this->days_since_checked >= $warning) {
            return 'warning';
        }

        return 'ok';
    }

    /**
     * Urgency tier using custom thresholds (for the configurable view).
     */
    public function urgencyWithThreshold(int $overdueDays, int $warningDays): string
    {
        if ($this->days_since_checked >= $overdueDays) {
            return 'overdue';
        }

        if ($this->days_since_checked >= $warningDays) {
            return 'warning';
        }

        return 'ok';
    }

    /**
     * Scope: pins that are overdue based on a given number of days.
     */
    public function scopeOverdue($query, ?int $days = null)
    {
        $days = $days ?? config('osaka.reminders.overdue_days', 30);
        $cutoff = now()->subDays($days);

        return $query->where(function ($q) use ($cutoff) {
            $q->where(function ($sub) use ($cutoff) {
                $sub->whereNotNull('last_checked_at')
                    ->where('last_checked_at', '<=', $cutoff);
            })->orWhere(function ($sub) use ($cutoff) {
                $sub->whereNull('last_checked_at')
                    ->where('created_at', '<=', $cutoff);
            });
        });
    }

    /**
     * Scope: pins in the warning zone (between warning and overdue).
     */
    public function scopeWarning($query, ?int $warningDays = null, ?int $overdueDays = null)
    {
        $warningDays = $warningDays ?? config('osaka.reminders.warning_days', 20);
        $overdueDays = $overdueDays ?? config('osaka.reminders.overdue_days', 30);
        $warningCutoff = now()->subDays($warningDays);
        $overdueCutoff = now()->subDays($overdueDays);

        return $query->where(function ($q) use ($warningCutoff, $overdueCutoff) {
            $q->where(function ($sub) use ($warningCutoff, $overdueCutoff) {
                $sub->whereNotNull('last_checked_at')
                    ->where('last_checked_at', '<=', $warningCutoff)
                    ->where('last_checked_at', '>', $overdueCutoff);
            })->orWhere(function ($sub) use ($warningCutoff, $overdueCutoff) {
                $sub->whereNull('last_checked_at')
                    ->where('created_at', '<=', $warningCutoff)
                    ->where('created_at', '>', $overdueCutoff);
            });
        });
    }

    /**
     * Scope: pins needing attention (warning + overdue).
     */
    public function scopeNeedsAttention($query, ?int $warningDays = null)
    {
        $warningDays = $warningDays ?? config('osaka.reminders.warning_days', 20);
        $cutoff = now()->subDays($warningDays);

        return $query->where(function ($q) use ($cutoff) {
            $q->where(function ($sub) use ($cutoff) {
                $sub->whereNotNull('last_checked_at')
                    ->where('last_checked_at', '<=', $cutoff);
            })->orWhere(function ($sub) use ($cutoff) {
                $sub->whereNull('last_checked_at')
                    ->where('created_at', '<=', $cutoff);
            });
        });
    }
}
