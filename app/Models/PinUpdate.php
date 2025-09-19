<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinUpdate extends Model
{
    protected $fillable = [
        'pin_id', 'status', 'photo', 'created_at'
    ];

    public function pin()
    {
        return $this->belongsTo(Pin::class);
    }
}
