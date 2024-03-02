<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'profile_picture',
        'weight',
        'height',
        'physical_activity_level',
        'goal',
        'password',
        'verified',
        'otp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'physical_activity_level' => 'json',
        'goal' => 'json',
        'verified' => 'boolean',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
