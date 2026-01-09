<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'id',
        'name'
    ];
    protected $table = 'branches';

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class, 'branch_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'branch_id');
    }
}
