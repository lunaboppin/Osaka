<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\XpService;
use App\Traits\Auditable;
use App\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, Auditable;

    /**
     * Attributes excluded from audit logging.
     */
    protected array $auditExclude = ['remember_token', 'total_xp'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'authentik_id',
        'avatar',
        'bio',
        'default_sticker_type_id',
        'total_xp',
        'xp_backfilled_at',
    ];

    protected $casts = [
        'total_xp' => 'integer',
        'xp_backfilled_at' => 'datetime',
    ];

    public function pins()
    {
        return $this->hasMany(Pin::class);
    }

    public function defaultStickerType()
    {
        return $this->belongsTo(StickerType::class, 'default_sticker_type_id');
    }

    public function xpTransactions()
    {
        return $this->hasMany(XpTransaction::class);
    }

    /**
     * Get the user's display avatar URL.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar;
    }

    // ── XP / Levelling accessors ────────────────────────────

    public function getLevelAttribute(): int
    {
        return app(XpService::class)->getLevel($this->total_xp ?? 0);
    }

    public function getLevelNameAttribute(): string
    {
        return app(XpService::class)->getLevelName($this->level);
    }

    public function getXpForNextLevelAttribute(): ?int
    {
        return app(XpService::class)->getXpForNextLevel($this->total_xp ?? 0);
    }

    public function getLevelProgressAttribute(): float
    {
        return app(XpService::class)->getLevelProgress($this->total_xp ?? 0);
    }

    public function getCurrentLevelThresholdAttribute(): int
    {
        return app(XpService::class)->getCurrentLevelThreshold($this->total_xp ?? 0);
    }

    public function getNextLevelThresholdAttribute(): ?int
    {
        return app(XpService::class)->getNextLevelThreshold($this->total_xp ?? 0);
    }
}
