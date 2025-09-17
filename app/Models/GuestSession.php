<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestSession extends Model
{
    protected $fillable = ['guest_uuid', 'ip', 'credits_remaining', 'expires_at'];
    public $dates = ['expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
