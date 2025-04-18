<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scores extends Model
{
    // use HasFactory;

    protected $fillable = ['player_id', 'level_id', 'score', 'count_of_stars', 'completed'];

    protected $guarded = ['id'];

}
