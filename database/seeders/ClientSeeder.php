<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'user_id' => 2,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'date' => '1990-01-01',
                'gender' => 'male',
            ],
        ];
        foreach ($clients as $client) {
            \App\Models\Client::create($client);
        }
    }
}
