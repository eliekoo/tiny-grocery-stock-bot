<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAlias extends Model
{
    protected $fillable = [
        'variant_id',
        'alias',
        'priority',
    ];

    protected $casts = [
        'priority' => 'integer',
    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }
}
