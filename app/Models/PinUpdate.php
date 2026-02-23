<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinUpdate extends Model
{
    protected $fillable = [
        'pin_id', 'user_id', 'status', 'photo', 'notes',
    ];

    public function pin()
    {
        return $this->belongsTo(Pin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
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
}
