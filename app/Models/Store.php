<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'id',
        'name',
        'branch_id'
    ];
    protected $table = 'stores';

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Transaction::class, 'store_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'store_id');
    }
}
