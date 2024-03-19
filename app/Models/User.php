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
        'firstName',
        'lastName',
        'email',
        'phone',
        'profilePicture',
        'gender',
        'age',
        'height',
        'weight',
        'weightType',
        'physicalActivityLevel',
        'goals',
        'emailVerifiedAt',
        'password',
        'verified',
        'otpConfirmed',
        'registrationType',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'emailVerifiedAt' => 'datetime',
        'password' => 'hashed',
        'physicalActivityLevel' => 'json',
        'goals' => 'json',
        'verified' => 'boolean',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
