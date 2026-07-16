<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'name',
        'unit',
        'minimum_stock',
        'barcode',
        'is_active',
    ];

    protected $casts = [
        'minimum_stock' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Display name used in Telegram
     */
    public function getDisplayNameAttribute()
    {
        return trim(
            collect([
                $this->brand,
                $this->variant,
                $this->size
            ])->filter()->implode(' ')
        );
    }

    public function aliases()
    {
        return $this->hasMany(ProductAlias::class);
    }
}
