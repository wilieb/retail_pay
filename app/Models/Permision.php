<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permision extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];
    protected $table = 'permisions';

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }
}
