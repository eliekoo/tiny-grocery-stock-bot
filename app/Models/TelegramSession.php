<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class TelegramSession extends Model
{

    protected $fillable = [
        'telegram_id',
        'state',
        'data',
    ];


    protected $casts = [
        'data' => 'array',
    ];
}
