<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pin extends Model
{
    public function updates()
    {
        return $this->hasMany(\App\Models\PinUpdate::class);
    }
    protected $fillable = [
        'title', 'latitude', 'longitude', 'status', 'photo', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
