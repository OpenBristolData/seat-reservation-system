<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Seat;

class SeatsTableSeeder extends Seeder
{
    public function run()
    {
        
        // Create 100 seats
     for ($i = 1; $i <= 100; $i++) {
    Seat::create([
        'seat_number' => 'S-' . str_pad($i, 3, '0', STR_PAD_LEFT), // S-001 to S-100
        'location' => 'Floor 4', // All seats on Floor 4
        'status' => 'available'
    ]);
}
    }
}