<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StickerType extends Model
{
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
}
