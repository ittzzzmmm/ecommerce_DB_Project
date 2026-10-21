<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin1',
            'email' => 'admin1@example.com',
            'password' => Hash::make('admin1password'),
        ]);

        User::create([
            'name' => 'Admin2',
            'email' => 'admin2@example.com',
            'password' => Hash::make('admin2password'),
        ]);

        User::create([
            'name' => 'TenTen',
            'email' => 'ten@example.com',
            'password' => Hash::make('tenpassword'),
        ]);

        User::create([
            'name' => 'it',
            'email' => 'it@example.com',
            'password' => Hash::make('itpassword'),
        ]);

        User::create([
            'name' => 'Yu Jung',
            'email' => 'yu@example.com',
            'password' => Hash::make('yupassword'),
        ]);

        User::create([
            'name' => 'Beam',
            'email' => 'beam@example.com',
            'password' => Hash::make('beampassword'),
        ]);
    }
}