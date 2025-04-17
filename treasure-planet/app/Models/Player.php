<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    // use HasFactory;

    // Поля, которые можно заполнять массово
    protected $fillable = ['username', 'email', 'avatar_id', 'password', 'hp', 'money'];

    // Поля, которые нельзя изменять
    protected $guarded = ['id'];

    // Отключение автоматических `created_at` и `updated_at`
    // public $timestamps = false;
}
