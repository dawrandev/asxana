<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Authentication Endpoints"
 * )
 */
class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
    ) {
        // 
    }

    /**
     * @OA\Post(
     * path="/api/v1/login",
     * summary="Tizimga kirish va token olish",
     * tags={"Auth"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"login","password"},
     * @OA\Property(property="login", type="string", example="admin123"),
     * @OA\Property(property="password", type="string", format="password", example="admin123")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Muvaffaqiyatli kirish",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="token", type="string", example="2|Xyz...123")
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Login yoki parol xato")
     * )
     */
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


    /**
     * @OA\Post(
     * path="/api/v1/register",
     * summary="Admin tomonidan yangi foydalanuvchi yaratish",
     * description="Ushbu metod faqat tizimga kirgan adminlar uchun ishlaydi va yangi foydalanuvchini avtomatik 'admin' roli bilan yaratadi.",
     * tags={"Auth"},
     * security={{"sanctum": {}}}, 
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"login","password","password_confirmation","phone"},
     * @OA\Property(property="login", type="string", example="yangi_admin"),
     * @OA\Property(property="password", type="string", format="password", example="parol123"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="parol123"),
     * @OA\Property(property="phone", type="string", example="+998901112233")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Foydalanuvchi muvaffaqiyatli yaratildi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="User created successfully"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="user", type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="login", type="string", example="yangi_admin"),
     * @OA\Property(property="phone", type="string", example="+998901112233"),
     * @OA\Property(property="role", type="string", example="admin")
     * )
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Avtorizatsiya xatosi (Token yo'q)"),
     * @OA\Response(response=403, description="Ruxsat yo'q (Faqat adminlar uchun)"),
     * @OA\Response(response=422, description="Validatsiya xatosi")
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->register($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'User Created successfully',
                'data' => [
                    'user' => $user,
                ],
                201
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error',
            ], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/v1/logout",
     * summary="Tizimdan chiqish (Tokenni bekor qilish)",
     * tags={"Auth"},
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Token muvaffaqiyatli o'chirildi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Successfully logged out")
     * )
     * ),
     * @OA\Response(response=401, description="Avtorizatsiyadan o'tilmagan")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }
            $this->authService->logout($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error',
            ], 500);
        }
    }
}
