<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    // use HasFactory;

    protected $table = 'tokens';

    protected $fillable = [
        'player_id',
        'token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
