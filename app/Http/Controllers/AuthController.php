<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Repositories\AuthRepository;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected AuthRepository $authRepository
    ) {
        // 
    }
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            [$user, $token] = $this->authService->login($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Login in successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
                200
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                422
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error',
            ], 500);
        }
    }

    // public function 
}
