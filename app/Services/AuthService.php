<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {
        // 
    }
    public function login(array $data): array
    {
        $user = $this->userRepository->findByLogin($data['login']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [$user, $token];
    }

    public function register(array $data): User
    {
        return $this->userRepository->create($data);
    }

    public function logout($user): bool
    {
        return $this->userRepository->revokeTokens($user);
    }
}
