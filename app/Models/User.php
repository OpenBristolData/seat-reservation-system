<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'reg_no',
        'name',
        'email',
        'password',
        'role',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    protected static function boot()
{
    parent::boot();

    static::creating(function ($user) {
        if (empty($user->reg_no)) {
            do {
                $regNo = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            } while (User::where('reg_no', $regNo)->exists());
            
            $user->reg_no = $regNo;
            
        }
    });
}
}