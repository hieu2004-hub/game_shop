<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\ProductInstance;
use App\Models\WarrantyClaim;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'userName', // Thay 'name' bằng 'userName'
        'email',
        'password',
        'phone',
        'address',
        'role', // Thêm 'role' vào fillable
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function productInstances()
    {
        return $this->hasMany(ProductInstance::class);
    }

    // public function warrantyClaims()
    // {
    //     return $this->hasMany(WarrantyClaim::class, 'claimed_by_user_id');
    // }
}
