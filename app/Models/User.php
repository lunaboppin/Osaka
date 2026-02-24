<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
    protected array $auditExclude = ['remember_token'];

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
    ];

    public function pins()
    {
        return $this->hasMany(Pin::class);
    }

    public function defaultStickerType()
    {
        return $this->belongsTo(StickerType::class, 'default_sticker_type_id');
    }

    /**
     * Get the user's display avatar URL.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar;
    }
}
