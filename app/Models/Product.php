<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'sku',
        'quantity',
        'units',
        'cost_per_unit',
        'retail_price',
        'status',
    ];

    protected $casts = [
        'quantity'       => 'integer',
        'cost_per_unit'  => 'decimal:2',
        'retail_price'   => 'decimal:2',
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(Transaction::class, 'product_id');
    }
}
