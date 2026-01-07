<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="Kategoriyalarni boshqarish uchun API endpointlar"
 * )
 */
class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService,
        protected CategoryRepository $categoryRepository
    ) {}

    /**
     * Barcha kategoriyalarni ro'yxatini olish
     * 
     * @OA\Get(
     *     path="/api/v1/categories",
     *     operationId="getCategoriesList",
     *     tags={"Categories"},
     *     summary="Barcha kategoriyalarni ro'yxatini olish",
     *     description="Tizimda mavjud barcha kategoriyalar ro'yxatini qaytaradi. Accept-Language header orqali kerakli tilda ma'lumot olishingiz mumkin.",
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="Tizim tilini belgilash uchun (uz, ru, kk). Default: uz",
     *         @OA\Schema(
     *             type="string",
     *             enum={"uz", "ru", "kk"},
     *             default="uz"
     *         ),
     *         example="uz"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Muvaffaqiyatli javob - kategoriyalar ro'yxati",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true,
     *                 description="So'rov muvaffaqiyatli bajarildi"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Categories retrieved successfully",
     *                 description="Xabar matni"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Kategoriyalar ro'yxati",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1, description="Kategoriya ID raqami"),
     *                     @OA\Property(property="name", type="string", example="Elektronika", description="Kategoriya nomi (tanlangan tilda)"),
     *                     @OA\Property(property="description", type="string", example="Barcha elektron qurilmalar", description="Kategoriya tavsifi"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-26T10:30:00Z", description="Yaratilgan vaqt"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-26T10:30:00Z", description="Yangilangan vaqt")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="code",
     *                 type="integer",
     *                 example=200,
     *                 description="HTTP status kodi"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server xatosi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve categories"),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
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
     * @OA\Post(
     * path="/api/v1/categories",
     * summary="Yangi kategoriya qo'shish",
     * tags={"Categories"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name"},
     * @OA\Property(
     * property="name",
     * type="object",
     * description="Kategoriya nomlari (til bo‘yicha)",
     * @OA\Property(property="kk", type="string", example="Patir"),
     * @OA\Property(property="uz", type="string", example="Pa'tir"),
     * @OA\Property(property="ru", type="string", example="Патир")
     * )
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Kategoriya muvaffaqiyatli yaratildi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Category created successfully"),
     * @OA\Property(
     * property="data",
     * type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="Pa'tir"),
     * @OA\Property(property="description", type="string", example=null),
     * @OA\Property(property="created_at", type="string", format="date-time"),
     * @OA\Property(property="updated_at", type="string", format="date-time")
     * ),
     * @OA\Property(property="code", type="integer", example=201)
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Validatsiya xatosi",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="The given data was invalid."),
     * @OA\Property(property="errors", type="object")
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Server xatosi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=false),
     * @OA\Property(property="message", type="string", example="Failed to create category"),
     * @OA\Property(property="data", type="null"),
     * @OA\Property(property="code", type="integer", example=500)
     * )
     * )
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
     * @OA\Get(
     * path="/api/v1/categories/{id}",
     * summary="ID bo'yicha kategoriyani olish (Barcha tillarda)",
     * description="Berilgan ID bo'yicha kategoriya ma'lumotlarini barcha tillardagi nomlari bilan qaytaradi.",
     * tags={"Categories"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="Kategoriya ID raqami",
     * required=true,
     * example=1,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Muvaffaqiyatli operatsiya",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Category retrieved successfully"),
     * @OA\Property(
     * property="data",
     * type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(
     * property="name",
     * type="object",
     * description="Kategoriya nomlari obyekt ko'rinishida",
     * @OA\Property(property="uz", type="string", example="Salatlar"),
     * @OA\Property(property="ru", type="string", example="Салаты"),
     * @OA\Property(property="kk", type="string", example="Salatlar")
     * ),
     * @OA\Property(property="created_at", type="string", format="date-time"),
     * @OA\Property(property="updated_at", type="string", format="date-time")
     * ),
     * @OA\Property(property="code", type="integer", example=200)
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Kategoriya topilmadi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=false),
     * @OA\Property(property="message", type="string", example="Category not found"),
     * @OA\Property(property="data", type="null"),
     * @OA\Property(property="code", type="integer", example=404)
     * )
     * )
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
     * @OA\Put(
     * path="/api/v1/categories/{id}",
     * summary="Mavjud kategoriyani yangilash",
     * tags={"Categories"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="Kategoriya ID raqami",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(
     * property="name",
     * type="object",
     * @OA\Property(property="kk", type="string", example="Yangi Patir"),
     * @OA\Property(property="uz", type="string", example="Yangi Pa'tir"),
     * @OA\Property(property="ru", type="string", example="Новый патир")
     * )
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Kategoriya muvaffaqiyatli yangilandi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Category updated successfully"),
     * @OA\Property(
     * property="data",
     * type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="Yangi Pa'tir"),
     * @OA\Property(property="created_at", type="string", format="date-time"),
     * @OA\Property(property="updated_at", type="string", format="date-time")
     * ),
     * @OA\Property(property="code", type="integer", example=200)
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Kategoriya topilmadi",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=false),
     * @OA\Property(property="message", type="string", example="Category not found"),
     * @OA\Property(property="code", type="integer", example=404)
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Validatsiya xatosi"
     * ),
     * @OA\Response(
     * response=500,
     * description="Server xatosi"
     * )
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
     *     description="ID bo'yicha kategoriyani tizimdan butunlay o'chiradi. ⚠️ Diqqat: Bu amal qaytarib bo'lmaydi! O'chirilgan kategoriyani qayta tiklash mumkin emas.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="O'chiriladigan kategoriya ID raqami *",
     *         required=true,
     *         example=1,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             minimum=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="Accept-Language",
     *         in="header",
     *         required=false,
     *         description="Tizim tilini belgilash uchun",
     *         @OA\Schema(type="string", enum={"uz", "ru", "kk"}, default="uz")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kategoriya muvaffaqiyatli o'chirildi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true, description="Operatsiya muvaffaqiyatli bajarildi"),
     *             @OA\Property(property="message", type="string", example="Category deleted successfully", description="O'chirish muvaffaqiyatli yakunlandi"),
     *             @OA\Property(property="data", type="null", description="O'chirishda qaytariladigan ma'lumot yo'q"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Kategoriya topilmadi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found", description="Berilgan ID bo'yicha kategoriya mavjud emas yoki avvalroq o'chirilgan"),
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
     *             @OA\Property(property="message", type="string", example="Failed to delete category", description="O'chirishda xatolik yuz berdi"),
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

    /**
     * JSON javob qaytaruvchi yordamchi metod
     * 
     * @param bool $status Operatsiya holati (true/false)
     * @param string $message Xabar matni
     * @param mixed $data Qaytariladigan ma'lumot
     * @param int $code HTTP status kodi
     * @return JsonResponse
     */
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
