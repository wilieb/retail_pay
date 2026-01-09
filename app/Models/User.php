<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'store_id',
        'branch_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    protected $table = 'users';

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function tokens()
    {
        return $this->hasMany(\App\Models\PersonalAccessToken::class, 'tokenable_id');
    }

    public function hasPermission($permissionName)
    {
        return $this->role->permissions()->where('name', $permissionName)->exists();
    }

    public function hasAnyPermission($permissions)
    {
        $permissions = is_array($permissions) ? $permissions : [$permissions];
        return $this->role->permissions()->whereIn('name', $permissions)->exists();
    }

    public function hasAllPermissions($permissions)
    {
        $permissions = is_array($permissions) ? $permissions : [$permissions];
        return $this->role->permissions()->whereIn('name', $permissions)->count() === count($permissions);
    }

    public function createToken($name, $abilities = ['*'], $expiresAt = null)
    {
        $plainTextToken = \Illuminate\Support\Str::random(40);
        $token = \App\Models\PersonalAccessToken::create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
            'tokenable_type' => self::class,
            'tokenable_id' => $this->id,
        ]);

        return new \Laravel\Sanctum\NewAccessToken($token, $plainTextToken);
    }
}
