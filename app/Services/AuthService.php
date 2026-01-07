<?php

namespace App\Services;

use App\Repositories\AuthRepository;

class AuthService
{
    public function __construct(
        protected AuthRepository $authRepository
    ) {
        // 
    }
    // 
}
