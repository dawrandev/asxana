<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="Kategoriyalarni boshqarish uchun API endpointlar"
 * )
 * 
 * 
 */
class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService,
        protected CategoryRepository $categoryRepository
    ) {}

    /**
     * @OA\Get(
     * path="/api/v1/categories",
     * operationId="getCategoriesList",
     * tags={"Categories"},
     * summary="Barcha kategoriyalarni ro'yxatini olish",
     * description="Tizimda mavjud barcha kategoriyalar ro'yxatini qaytaradi. Bu endpoint autentifikatsiya talab qilmaydi.",
     * * @OA\Parameter(
     * name="Accept-Language",
     * in="header",
     * required=false,
     * description="Tizim tilini belgilash uchun (uz, ru, en)",
     * @OA\Schema(
     * type="string",
     * enum={"uz", "ru", "en"},
     * default="uz"
     * )
     * ),
     *
     * @OA\Response(
     * response=200,
     * description="Muvaffaqiyatli javob",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Categories retrieved successfully"),
     * @OA\Property(
     * property="data",
     * type="array",
     * @OA\Items(
     * type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="Elektronika"),
     * @OA\Property(property="description", type="string", example="Barcha elektron qurilmalar"),
     * @OA\Property(property="products_count", type="integer", example=25),
     * @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00.000000Z"),
     * @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T10:30:00.000000Z")
     * )
     * ),
     * @OA\Property(property="code", type="integer", example=200)
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Server xatosi",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="success", type="boolean", example=false),
     * @OA\Property(property="message", type="string", example="Failed to retrieve categories"),
     * @OA\Property(property="data", type="null"),
     * @OA\Property(property="code", type="integer", example=500)
     * )
     * )
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $categories = $this->categoryRepository->getAllCategories();
            $data = CategoryResource::collection($categories);
            return $this->jsonResponse(true, 'Categories retrieved successfully', $data, 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Failed to retrieve categories', null, 500);
        }
    }

    /**
     * Yangi kategoriya yaratish
     * 
     * @OA\Post(
     *     path="/api/v1/categories",
     *     operationId="storeCategory",
     *     tags={"Categories"},
     *     summary="Yangi kategoriya qo'shish",
     *     description="Tizimga yangi kategoriya qo'shadi. Nom maydonlari ko'p tillidir (uz, ru, en).",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Kategoriya ma'lumotlari",
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(
     *                 property="name",
     *                 type="object",
     *                 description="Kategoriya nomi (ko'p tilli)",
     *                 required={"uz"},
     *                 @OA\Property(property="uz", type="string", example="Elektronika", description="O'zbek tilida nom"),
     *                 @OA\Property(property="ru", type="string", example="Электроника", description="Rus tilida nom"),
     *                 @OA\Property(property="en", type="string", example="Electronics", description="Ingliz tilida nom")
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="object",
     *                 description="Kategoriya tavsifi (ko'p tilli)",
     *                 @OA\Property(property="uz", type="string", example="Barcha elektron qurilmalar", description="O'zbek tilida tavsif"),
     *                 @OA\Property(property="ru", type="string", example="Все электронные устройства", description="Rus tilida tavsif"),
     *                 @OA\Property(property="en", type="string", example="All electronic devices", description="Ingliz tilida tavsif")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Kategoriya muvaffaqiyatli yaratildi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=5),
     *                 @OA\Property(property="name", type="string", example="Elektronika"),
     *                 @OA\Property(property="description", type="string", example="Barcha elektron qurilmalar"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="code", type="integer", example=201)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validatsiya xatosi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The name.uz field is required."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="name.uz",
     *                     type="array",
     *                     @OA\Items(type="string", example="The name.uz field is required.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server xatosi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to create category"),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $category = $this->categoryService->createCategory($request->validated());
            $data = new CategoryResource($category);
            return $this->jsonResponse(true, 'Category created successfully', $data, 201);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Failed to create category', null, 500);
        }
    }

    /**
     * Bitta kategoriyani ko'rish
     * 
     * @OA\Get(
     *     path="/api/v1/categories/{id}",
     *     operationId="getCategoryById",
     *     tags={"Categories"},
     *     summary="ID bo'yicha kategoriyani olish",
     *     description="Berilgan ID raqami bo'yicha kategoriya ma'lumotlarini qaytaradi",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Kategoriya ID raqami",
     *         required=true,
     *         example=1,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kategoriya topildi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Elektronika"),
     *                 @OA\Property(property="description", type="string", example="Barcha elektron qurilmalar"),
     *                 @OA\Property(property="products_count", type="integer", example=25),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Kategoriya topilmadi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found"),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server xatosi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve category"),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $category = $this->categoryService->getCategoryById($id);

            if (!$category) {
                return $this->jsonResponse(false, 'Category not found', null, 404);
            }

            $data = new CategoryResource($category);
            return $this->jsonResponse(true, 'Category retrieved successfully', $data, 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Failed to retrieve category', null, 500);
        }
    }

    /**
     * Kategoriyani yangilash
     * 
     * @OA\Put(
     *     path="/api/v1/categories/{id}",
     *     operationId="updateCategory",
     *     tags={"Categories"},
     *     summary="Mavjud kategoriyani yangilash",
     *     description="ID bo'yicha kategoriya ma'lumotlarini yangilaydi. Barcha maydonlar ixtiyoriy.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Kategoriya ID raqami",
     *         required=true,
     *         example=1,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Yangilanishi kerak bo'lgan ma'lumotlar",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="name",
     *                 type="object",
     *                 description="Kategoriya nomi (ko'p tilli)",
     *                 @OA\Property(property="uz", type="string", example="Yangi Elektronika"),
     *                 @OA\Property(property="ru", type="string", example="Новая Электроника"),
     *                 @OA\Property(property="en", type="string", example="New Electronics")
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="object",
     *                 description="Kategoriya tavsifi (ko'p tilli)",
     *                 @OA\Property(property="uz", type="string", example="Yangilangan tavsif"),
     *                 @OA\Property(property="ru", type="string", example="Обновленное описание"),
     *                 @OA\Property(property="en", type="string", example="Updated description")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kategoriya muvaffaqiyatli yangilandi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Yangi Elektronika"),
     *                 @OA\Property(property="description", type="string", example="Yangilangan tavsif"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Kategoriya topilmadi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found"),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validatsiya xatosi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server xatosi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to update category"),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function update(UpdateCategoryRequest $request, string $id): JsonResponse
    {
        try {
            $category = $this->categoryService->getCategoryById($id);

            if (!$category) {
                return $this->jsonResponse(false, 'Category not found', null, 404);
            }

            $updatedCategory = $this->categoryService->updateCategory($category, $request->validated());
            $data = new CategoryResource($updatedCategory);
            return $this->jsonResponse(true, 'Category updated successfully', $data, 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Failed to update category', null, 500);
        }
    }

    /**
     * Kategoriyani o'chirish
     * 
     * @OA\Delete(
     *     path="/api/v1/categories/{id}",
     *     operationId="deleteCategory",
     *     tags={"Categories"},
     *     summary="Kategoriyani o'chirish",
     *     description="ID bo'yicha kategoriyani tizimdan o'chiradi. Bu amal qaytarib bo'lmaydi!",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="O'chiriladigan kategoriya ID raqami",
     *         required=true,
     *         example=1,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kategoriya muvaffaqiyatli o'chirildi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category deleted successfully"),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Kategoriya topilmadi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found"),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server xatosi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to delete category"),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $category = $this->categoryService->getCategoryById($id);

            if (!$category) {
                return $this->jsonResponse(false, 'Category not found', null, 404);
            }

            $this->categoryService->deleteCategory($category);
            return $this->jsonResponse(true, 'Category deleted successfully', null, 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Failed to delete category', null, 500);
        }
    }

    public function jsonResponse($status, $message, $data, $code): JsonResponse
    {
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data'    => $data,
            'code'    => $code
        ], $code);
    }
}
