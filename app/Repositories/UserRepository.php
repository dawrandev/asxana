<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function findByLogin(string $login)
    {
        return User::where('login', $login)->first();
    }

    public function create(array $data): User
    {
        return User::create([
            'login' => $data['login'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'role' => 'admin',
        ]);
    }

    public function revokeTokens($user): bool
    {
        return $user->currentAccessToken()->delete();
    }
}
