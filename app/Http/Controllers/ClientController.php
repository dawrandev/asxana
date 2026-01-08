<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Repositories\ClientRepository;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientController extends Controller
{
    public function __construct(
        protected ClientService $clientService,
        protected ClientRepository $clientRepository
    ) {
        // 
    }
    public function index(Request $request): JsonResponse
    {
        try {
            $perpage = $request->query('perpage', 15);

            $clients = $this->clientRepository->getClients($perpage);

            $data = ClientResource::collection($clients);

            return $this->jsonResponse(true, 'Clients retrieved successfully', [
                'clients' => $data,
                'pagination' => [
                    'total' => $clients->total(),
                    'count' => $clients->count(),
                    'per_page' => $clients->perPage(),
                    'current_page' => $clients->currentPage(),
                    'total_pages' => $clients->lastPage(),
                ],
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
