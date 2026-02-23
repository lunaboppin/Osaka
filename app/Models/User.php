<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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
    ];

    public function pins()
    {
        return $this->hasMany(Pin::class);
    }

    /**
     * Get the user's display avatar URL.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar;
    }
}
