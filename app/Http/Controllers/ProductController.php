<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="Mahsulotlarni boshqarish uchun API endpointlar"
 * )
 */
class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected ProductRepository $productRepository
    ) {}
    /**
     * @OA\Get(
     * path="/api/v1/products",
     * summary="Mahsulotlar ro'yxatini olish",
     * description="Paginatsiya va filtrlar bilan mahsulotlar ro'yxatini qaytaradi",
     * tags={"Products"},
     * @OA\Parameter(
     * name="search",
     * in="query",
     * description="Nomi yoki tavsifi bo'yicha qidiruv",
     * required=false,
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="per_page",
     * in="query",
     * description="Har bir sahifadagi mahsulotlar soni",
     * required=false,
     * @OA\Schema(type="integer", default=15)
     * ),
     * @OA\Parameter(
     * name="page",
     * in="query",
     * description="Sahifa raqami",
     * required=false,
     * @OA\Schema(type="integer", default=1)
     * ),
     * @OA\Parameter(
     * name="category_id",
     * in="query",
     * description="Kategoriya bo'yicha filtralash",
     * required=false,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Muvaffaqiyatli yakunlandi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Products retrieved successfully"),
     * @OA\Property(
     * property="data",
     * type="object",
     * @OA\Property(
     * property="items",
     * type="array",
     * description="Mahsulotlar ro'yxati",
     * @OA\Items(ref="#/components/schemas/ProductResource")
     * ),
     * @OA\Property(
     * property="pagination",
     * type="object",
     * description="Paginatsiya ma'lumotlari",
     * @OA\Property(property="current_page", type="integer", example=1),
     * @OA\Property(property="last_page", type="integer", example=10),
     * @OA\Property(property="per_page", type="integer", example=15),
     * @OA\Property(property="total", type="integer", example=150)
     * )
     * ),
     * @OA\Property(property="code", type="integer", example=200)
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Server xatosi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=false),
     * @OA\Property(property="message", type="string", example="Failed to retrieve products"),
     * @OA\Property(property="data", type="null"),
     * @OA\Property(property="code", type="integer", example=500)
     * )
     * )
     * )
     */
    public function index(ProductFilterRequest $request): JsonResponse
    {
        try {
            $products = $this->productService->getProducts($request->validated());

            $resourceCollection = ProductResource::collection($products);

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $resourceCollection->response()->getData()->data,
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ],
                'code' => 200
            ], 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Failed to retrieve products', null, 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/products/{id}",
     * summary="Bitta mahsulot tafsilotini olish",
     * description="ID orqali mahsulot ma'lumotlarini qaytaradi. Headerda yuborilgan tilga qarab nom va tavsif o'zgaradi.",
     * tags={"Products"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="Mahsulotning ID raqami",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Parameter(
     * name="Accept-Language",
     * in="header",
     * description="Tilni tanlash (uz, ru, kk)",
     * required=false,
     * @OA\Schema(type="string", default="uz")
     * ),
     * @OA\Response(
     * response=200,
     * description="Muvaffaqiyatli yakunlandi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Product retrieved successfully"),
     * @OA\Property(
     * property="data",
     * type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="category_name", type="string", example="Salat"),
     * @OA\Property(property="image", type="string", example="sezar-salat.jpg"),
     * @OA\Property(property="price", type="integer", example=25000),
     * @OA\Property(property="name", type="string", example="Sezar salat"),
     * @OA\Property(property="description", type="string", example="Tovuq go'shti, parmezan bilan klassik Sezar salat"),
     * @OA\Property(property="is_available", type="boolean", example=true),
     * @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-22T04:52:34.000000Z"),
     * @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-22T04:52:34.000000Z")
     * ),
     * @OA\Property(property="code", type="integer", example=200)
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Mahsulot topilmadi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=false),
     * @OA\Property(property="message", type="string", example="Product not found"),
     * @OA\Property(property="data", type="null"),
     * @OA\Property(property="code", type="integer", example=404)
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Server xatosi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=false),
     * @OA\Property(property="message", type="string", example="Failed to retrieve product"),
     * @OA\Property(property="data", type="null"),
     * @OA\Property(property="code", type="integer", example=500)
     * )
     * )
     * )
     */

    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productRepository->getProductById($id);

            if (!$product) {
                return $this->jsonResponse(false, 'Product not found', null, 404);
            }

            $data = new ProductResource($product);

            return $this->jsonResponse(true, 'Product retrieved successfully', $data, 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Failed to retrieve product', null, 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/v1/products",
     * summary="Yangi mahsulot yaratish",
     * description="Mahsulot asosiy ma'lumotlari va bir nechta tildagi tarjimalarini rasm bilan birga saqlaydi.",
     * tags={"Products"},
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"category_id", "price", "image", "name[kk]", "name[uz]", "name[ru]"},
     * @OA\Property(property="category_id", type="integer", example=1, description="Kategoriya ID raqami"),
     * @OA\Property(property="price", type="integer", example=25000, description="Mahsulot narxi"),
     * @OA\Property(property="is_available", type="boolean", example=true, description="Sotuvda bor yoki yo'qligi"),
     * @OA\Property(property="image", type="string", format="binary", description="Mahsulot rasmi (fayl)"),
     * * @OA\Property(property="name[kk]", type="string", example="Sezar salat", description="Mahsulot nomi (kk)"),
     * @OA\Property(property="name[uz]", type="string", example="Sezar salat", description="Mahsulot nomi (uz)"),
     * @OA\Property(property="name[ru]", type="string", example="Сезар салат", description="Mahsulot nomi (ru)"),
     * * @OA\Property(property="description[kk]", type="string", example="Tovuq go'shti bilan", description="Mahsulot tavsifi (kk)"),
     * @OA\Property(property="description[uz]", type="string", example="Tovuq go'shti bilan", description="Mahsulot tavsifi (uz)"),
     * @OA\Property(property="description[ru]", type="string", example="С курицей", description="Mahsulot tavsifi (ru)")
     * )
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Mahsulot muvaffaqiyatli yaratildi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Product created successfully"),
     * @OA\Property(property="data", type="object", ref="#/components/schemas/ProductResource"),
     * @OA\Property(property="code", type="integer", example=201)
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Validatsiya xatosi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=false),
     * @OA\Property(property="message", type="string", example="Validation error"),
     * @OA\Property(property="errors", type="object")
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Serverda xatolik yuz berdi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=false),
     * @OA\Property(property="message", type="string", example="Failed to create product")
     * )
     * )
     * )
     */

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->createProduct($request->validated());

            $data = new ProductResource($product);

            return $this->jsonResponse(true, 'Product created successfully', $data, 201);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Failed to create product: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * @OA\PUT(
     * path="/api/v1/products/{id}",
     * summary="Mahsulotni tahrirlash",
     * description="Eslatma: Rasm yuklashda muammo bo'lmasligi uchun POST metodidan foydalaning va body'da '_method=PUT' yuboring.",
     * tags={"Products"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="Mahsulot ID raqami",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * @OA\Property(property="_method", type="string", example="PUT"),
     * @OA\Property(property="category_id", type="integer", example=1),
     * @OA\Property(property="price", type="integer", example=30000),
     * @OA\Property(property="is_available", type="boolean", example=true),
     * @OA\Property(property="image", type="string", format="binary"),
     * @OA\Property(
     * property="name",
     * type="object",
     * @OA\Property(property="kk", type="string", example="Yangi nom"),
     * @OA\Property(property="uz", type="string", example="Yangi nom"),
     * @OA\Property(property="ru", type="string", example="Новый название"),
     * ),
     * @OA\Property(
     * property="description",
     * type="object",
     * @OA\Property(property="kk", type="string", example="Yangi tavsif"),
     * @OA\Property(property="uz", type="string", example="Yangi tavsif"),
     * @OA\Property(property="ru", type="string", example="Новые описание"),
     * ),
     * )
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Muvaffaqiyatli yangilandi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="data", ref="#/components/schemas/ProductResource")
     * )
     * ),
     * @OA\Response(response=404, description="Mahsulot topilmadi")
     * )
     */

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $product = $this->productRepository->getProductById($id);

            if (!$product) {
                return $this->jsonResponse(false, 'Product not found', null, 404);
            }

            $updatedProduct = $this->productService->updateProduct($product, $request->validated());

            return $this->jsonResponse(true, 'Product updated successfully', new ProductResource($updatedProduct), 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Failed to update product', null, 500);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/v1/products/{id}",
     * summary="Mahsulotni o'chirish",
     * description="Mahsulotni bazadan va unga tegishli rasmni server xotirasidan butunlay o'chirib tashlaydi.",
     * tags={"Products"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="O'chirilishi kerak bo'lgan mahsulotning ID raqami",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * example=1
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Mahsulot muvaffaqiyatli o'chirildi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Product deleted successfully"),
     * @OA\Property(property="data", type="null"),
     * @OA\Property(property="code", type="integer", example=200)
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Mahsulot topilmadi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=false),
     * @OA\Property(property="message", type="string", example="Product not found")
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Serverda xatolik yuz berdi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=false),
     * @OA\Property(property="message", type="string", example="Failed to delete product")
     * )
     * )
     * )
     */

    public function destroy(int $id): JsonResponse
    {
        try {
            $product = $this->productRepository->getProductById($id);

            if (!$product) {
                return $this->jsonResponse(false, 'Product not found', null, 404);
            }

            $this->productService->deleteProduct($product);

            return $this->jsonResponse(true, 'Product deleted successfully', null, 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Failed to delete product', null, 500);
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
