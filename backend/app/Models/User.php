<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'remember_token',
        'is_active',
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

    protected $with = ['profile', 'roles'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function likedProducts()
    {
        return $this->belongsToMany(Product::class, 'liked_products')->withTimestamps();
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'created_by');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'created_by');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }

    public function stockMovemenets()
    {
        return $this->hasMany(StockMovement::class, 'created_by');
    }
    
    protected static function booted(): void
    {
        static::created(function (User $user) {
            $user->profile()->create([
                'bio' => 'Newly created at ' . date('Y-m-d'),
            ]);
        });
    }
}
