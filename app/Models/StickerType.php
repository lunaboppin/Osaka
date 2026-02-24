<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StickerType extends Model
{
    use Auditable;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'color',
    ];

    public function pins()
    {
        return $this->hasMany(Pin::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_name');
    }

    /**
     * Resolve the current active sticker type ID.
     * Priority: session value > authenticated user's default > null (all).
     */
    public static function currentId(): ?int
    {
        // If session has been explicitly set (even to null for "All"), use that
        if (session()->has('current_sticker_type_id')) {
            $id = session('current_sticker_type_id');
            return $id ? (int) $id : null;
        }

        // Fall back to authenticated user's default
        if ($user = Auth::user()) {
            return $user->default_sticker_type_id;
        }

        return null;
    }
}
