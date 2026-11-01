<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin1@example.com'], [
            'name' => 'Admin1',
            'email' => 'admin1@example.com',
            'password' => Hash::make('admin1password'),
        ]);

        User::firstOrCreate(['email' => 'admin2@example.com'], [
            'name' => 'Admin2',
            'email' => 'admin2@example.com',
            'password' => Hash::make('admin2password'),
        ]);

        User::firstOrCreate(['email' => 'ten@example.com'], [
            'name' => 'TenTen',
            'email' => 'ten@example.com',
            'password' => Hash::make('tenpassword'),
        ]);

        User::firstOrCreate(['email' => 'it@example.com'], [
            'name' => 'it',
            'email' => 'it@example.com',
            'password' => Hash::make('itpassword'),
        ]);

        User::firstOrCreate(['email' => 'yu@example.com'], [
            'name' => 'Yu Jung',
            'email' => 'yu@example.com',
            'password' => Hash::make('yupassword'),
        ]);

        User::firstOrCreate(['email' => 'beam@example.com'], [
            'name' => 'Beam',
            'email' => 'beam@example.com',
            'password' => Hash::make('beampassword'),
        ]);
    }
}
