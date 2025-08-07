<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'seat_number',
        'location',
        'status'
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

   // app/Models/Seat.php
public function isAvailableForDate($date)
{
    return !$this->reservations()
        ->where('reservation_date', $date)
        ->where('status', 'active') // Only check active reservations
        ->exists();
}
}