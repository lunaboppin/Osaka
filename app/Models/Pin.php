<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pin extends Model
{
    protected $fillable = [
        'title', 'latitude', 'longitude', 'status', 'photo', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
