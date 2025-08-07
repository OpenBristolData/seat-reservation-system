<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    // database/seeders/UsersTableSeeder.php
public function run()
{
    User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'reg_no' => '0000', // Special admin code
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);

    for ($i = 1; $i <= 3; $i++) {
        User::create([
            'name' => 'Intern ' . $i,
            'email' => 'intern' . $i . '@example.com',
            // reg_no will auto-generate
            'password' => Hash::make('password'),
            'role' => 'intern',
        ]);
    }
}
}