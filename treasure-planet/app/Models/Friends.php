<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Friends extends Model
{
    protected $fillable = ['player_id', 'friend_id', 'status'];

    // Поля, которые нельзя изменять
    protected $guarded = ['id'];
}
