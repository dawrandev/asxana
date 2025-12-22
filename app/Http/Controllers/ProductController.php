<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFilterRequest;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected ProductRepository $productRepository
    ) {}

    public function index(ProductFilterRequest $request): JsonResponse
    {
        try {
            $products = $this->productService->getProducts($request->validated());

            $data = ProductResource::collection($products);

            return $this->jsonResponse(true, 'Products retrieved successfully', $data, 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Failed to retrieve products', null, 500);
        }
    }

    public function jsonResponse($status, $message, $data, $code)
    {
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data'    => $data,
            'code'    => $code
        ]);
    }
}
