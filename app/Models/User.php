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
        'banner_path',
        'accent_color',
        'profile_theme',
        'avatar_frame',
        'displayed_badges',
        'bio',
        'default_sticker_type_id',
        'total_xp',
        'xp_backfilled_at',
    ];

    protected $casts = [
        'total_xp' => 'integer',
        'xp_backfilled_at' => 'datetime',
        'displayed_badges' => 'array',
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

    /**
     * Full URL for the profile banner image.
     */
    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner_path ? asset('storage/' . $this->banner_path) : null;
    }

    /**
     * Get the effective accent color (user-chosen or theme default).
     */
    public function getEffectiveAccentColorAttribute(): string
    {
        if ($this->accent_color) {
            return $this->accent_color;
        }

        $theme = $this->profile_theme ?? 'default';
        return config("osaka.profile.themes.{$theme}.accent", '#D4A843');
    }

    /**
     * Get the theme configuration array for this user.
     */
    public function getThemeConfigAttribute(): array
    {
        $theme = $this->profile_theme ?? 'default';
        return config("osaka.profile.themes.{$theme}", config('osaka.profile.themes.default'));
    }

    /**
     * Get the avatar frame config, checking level requirements.
     */
    public function getAvatarFrameConfigAttribute(): ?array
    {
        if (!$this->avatar_frame) {
            return null;
        }

        $frame = config("osaka.profile.avatar_frames.{$this->avatar_frame}");
        if (!$frame) {
            return null;
        }

        // Check level requirement
        if (($frame['min_level'] ?? 1) > $this->level) {
            return null;
        }

        return $frame;
    }

    /**
     * Get all avatar frames this user has unlocked.
     */
    public function getUnlockedFramesAttribute(): array
    {
        $all = config('osaka.profile.avatar_frames', []);
        $level = $this->level;

        return collect($all)
            ->filter(fn($frame) => ($frame['min_level'] ?? 1) <= $level)
            ->all();
    }

    /**
     * All available badge keys this user can display.
     */
    public function getAvailableBadgesAttribute(): array
    {
        $badges = [];

        // Level-based title badge
        $badges['level'] = [
            'label' => "Level {$this->level} — {$this->level_name}",
            'icon' => 'star',
            'color' => '#D4A843',
        ];

        // Pin count badges
        $pinCount = $this->pins()->count();
        if ($pinCount >= 1) {
            $badges['pin_collector'] = [
                'label' => "Pin Collector ({$pinCount})",
                'icon' => 'map-pin',
                'color' => '#C41E3A',
            ];
        }
        if ($pinCount >= 10) {
            $badges['pin_enthusiast'] = [
                'label' => 'Pin Enthusiast (10+)',
                'icon' => 'map-pin',
                'color' => '#10B981',
            ];
        }
        if ($pinCount >= 50) {
            $badges['pin_master'] = [
                'label' => 'Pin Master (50+)',
                'icon' => 'map-pin',
                'color' => '#8B5CF6',
            ];
        }

        // XP milestones
        $xp = $this->total_xp ?? 0;
        if ($xp >= 100) {
            $badges['xp_100'] = [
                'label' => '100 XP Club',
                'icon' => 'bolt',
                'color' => '#F59E0B',
            ];
        }
        if ($xp >= 500) {
            $badges['xp_500'] = [
                'label' => '500 XP Club',
                'icon' => 'bolt',
                'color' => '#EF4444',
            ];
        }
        if ($xp >= 1000) {
            $badges['xp_1000'] = [
                'label' => '1K XP Club',
                'icon' => 'bolt',
                'color' => '#8B5CF6',
            ];
        }

        // Early adopter (registered before certain date or low ID)
        if ($this->id <= 10) {
            $badges['early_adopter'] = [
                'label' => 'Early Adopter',
                'icon' => 'clock',
                'color' => '#06B6D4',
            ];
        }

        // Profile completed
        if ($this->bio && $this->avatar) {
            $badges['profile_complete'] = [
                'label' => 'Profile Complete',
                'icon' => 'user-check',
                'color' => '#10B981',
            ];
        }

        return $badges;
    }

    /**
     * Get the badges the user has chosen to display (max 5), filtered to available ones.
     */
    public function getDisplayedBadgeDetailsAttribute(): array
    {
        $chosen = $this->displayed_badges ?? [];
        $available = $this->available_badges;

        return collect($chosen)
            ->filter(fn($key) => isset($available[$key]))
            ->take(5)
            ->map(fn($key) => array_merge($available[$key], ['key' => $key]))
            ->values()
            ->all();
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
