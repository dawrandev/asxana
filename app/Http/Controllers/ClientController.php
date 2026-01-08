<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Repositories\ClientRepository;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @OA\Tag(
 *     name="Clients",
 *     description="Klientlarni ko'rish uchun API endpointlar"
 * )
 */

class ClientController extends Controller
{
    public function __construct(
        protected ClientService $clientService,
        protected ClientRepository $clientRepository
    ) {
        // 
    }

    /**
     * @OA\Get(
     * path="/api/v1/clients",
     * summary="Mijozlar ro'yxatini olish",
     * description="Barcha mijozlarni sahifalangan (pagination) holda qaytaradi. Faqat avtorizatsiyadan o'tgan foydalanuvchilar uchun.",
     * tags={"Clients"},
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="per_page",
     * in="query",
     * description="Sahifadagi elementlar soni (default: 15)",
     * required=false,
     * @OA\Schema(type="integer", example=15)
     * ),
     * @OA\Parameter(
     * name="page",
     * in="query",
     * description="Sahifa raqami",
     * required=false,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Mijozlar ro'yxati muvaffaqiyatli qaytarildi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Clients retrieved successfully"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="clients", type="array",
     * @OA\Items(
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="user_id", type="integer", example=2),
     * @OA\Property(property="first_name", type="string", example="John"),
     * @OA\Property(property="last_name", type="string", example="Doe"),
     * @OA\Property(property="date", type="string", format="date", example="1990-01-01"),
     * @OA\Property(property="gender", type="string", example="male")
     * )
     * ),
     * @OA\Property(property="pagination", type="object",
     * @OA\Property(property="total", type="integer", example=100),
     * @OA\Property(property="count", type="integer", example=15),
     * @OA\Property(property="per_page", type="integer", example=15),
     * @OA\Property(property="current_page", type="integer", example=1),
     * @OA\Property(property="total_pages", type="integer", example=7)
     * )
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Avtorizatsiyadan o'tilmagan"),
     * @OA\Response(response=500, description="Server xatosi")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perpage = $request->query('perpage', 15);

            $clients = $this->clientRepository->getClients($perpage);

            $data = ClientResource::collection($clients);

            return response()->json([
                "success" => true,
                "message" => "Clients retrieved successfully",
                "data" => ClientResource::collection($clients),
                "pagination" => [
                    "total" => $clients->total(),
                    "per_page" => $clients->perPage(),
                    "current_page" => $clients->currentPage(),
                    "total_pages" => $clients->lastPage()
                ]
            ], 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Failed to retrieve clients', null, 500);
        }
    }

    public function jsonResponse($status, $message, $data, $code)
    {
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
