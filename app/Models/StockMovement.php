<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'variant_id',
        'type',
        'quantity',
        'balance_after',
        'remarks',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];


    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }
}
