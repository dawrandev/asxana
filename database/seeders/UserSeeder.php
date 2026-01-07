<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'login'    => 'admin123',
            'password' => Hash::make('admin123'),
            'phone'    => '+998901234567',
            'role'     => 'admin',
        ]);

        User::create([
            'login'    => 'client_user',
            'password' => Hash::make('password123'),
            'phone'    => '+998907654321',
            'role'     => 'client',
        ]);
    }
}
