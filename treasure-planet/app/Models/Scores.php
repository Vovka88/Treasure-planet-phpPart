<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scores extends Model
{
    // use HasFactory;

    protected $fillable = ['player_id', 'level_id', 'score', 'count_os_stars', 'compleated'];

    protected $guarded = ['id'];

}
