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

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService,
        protected CategoryRepository $categoryRepository
    ) {}

    /**
     * Get all categories
     *
     * @group Category Management
     * @responseField success boolean Operation success status
     * @responseField message string Response message
     * @responseField data array List of categories with translations
     * @responseField data[].id integer Category ID
     * @responseField data[].translations array Category translations in all languages
     * @responseField data[].translations[].id integer Translation ID
     * @responseField data[].translations[].category_id integer Foreign key reference to category
     * @responseField data[].translations[].lang_code string Language code (qq, uz, or ru)
     * @responseField data[].translations[].name string Category name in specified language
     * @responseField data[].translations[].created_at string Translation creation timestamp (ISO 8601 format)
     * @responseField data[].translations[].updated_at string Translation last update timestamp (ISO 8601 format)
     * @responseField data[].created_at string Category creation timestamp (ISO 8601 format)
     * @responseField data[].updated_at string Category last update timestamp (ISO 8601 format)
     * @responseField code integer HTTP status code
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Categories retrieved successfully",
     *   "data": [
     *     {
     *       "id": 1,
     *       "translations": [
     *         {
     *           "id": 1,
     *           "category_id": 1,
     *           "lang_code": "qq",
     *           "name": "Salat",
     *           "created_at": "2024-01-15T10:30:00.000000Z",
     *           "updated_at": "2024-01-15T10:30:00.000000Z"
     *         },
     *         {
     *           "id": 2,
     *           "category_id": 1,
     *           "lang_code": "uz",
     *           "name": "Salat",
     *           "created_at": "2024-01-15T10:30:00.000000Z",
     *           "updated_at": "2024-01-15T10:30:00.000000Z"
     *         },
     *         {
     *           "id": 3,
     *           "category_id": 1,
     *           "lang_code": "ru",
     *           "name": "Салат",
     *           "created_at": "2024-01-15T10:30:00.000000Z",
     *           "updated_at": "2024-01-15T10:30:00.000000Z"
     *         }
     *       ],
     *       "created_at": "2024-01-15T10:30:00.000000Z",
     *       "updated_at": "2024-01-15T10:30:00.000000Z"
     *     },
     *     {
     *       "id": 2,
     *       "translations": [
     *         {
     *           "id": 4,
     *           "category_id": 2,
     *           "lang_code": "qq",
     *           "name": "Hot-dog",
     *           "created_at": "2024-01-15T10:31:00.000000Z",
     *           "updated_at": "2024-01-15T10:31:00.000000Z"
     *         },
     *         {
     *           "id": 5,
     *           "category_id": 2,
     *           "lang_code": "uz",
     *           "name": "Hot-dog",
     *           "created_at": "2024-01-15T10:31:00.000000Z",
     *           "updated_at": "2024-01-15T10:31:00.000000Z"
     *         },
     *         {
     *           "id": 6,
     *           "category_id": 2,
     *           "lang_code": "ru",
     *           "name": "Хот-дог",
     *           "created_at": "2024-01-15T10:31:00.000000Z",
     *           "updated_at": "2024-01-15T10:31:00.000000Z"
     *         }
     *       ],
     *       "created_at": "2024-01-15T10:31:00.000000Z",
     *       "updated_at": "2024-01-15T10:31:00.000000Z"
     *     }
     *   ],
     *   "code": 200
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Failed to retrieve categories",
     *   "data": null,
     *   "code": 500
     * }
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
     * Create a new category
     *
     * @group Category Management
     * @bodyParam translations array required Array of translation objects (minimum 1). Example: [{"lang_code": "qq", "name": "Pizza"}, {"lang_code": "uz", "name": "Pitsa"}, {"lang_code": "ru", "name": "Пицца"}]
     * @bodyParam translations[].lang_code string required Language code (qq, uz, or ru). Example: qq
     * @bodyParam translations[].name string required Category name in specified language (max 255 characters). Example: Pizza
     * 
     * @responseField success boolean Operation success status
     * @responseField message string Response message
     * @responseField data object Created category with translations
     * @responseField data.id integer Category ID
     * @responseField data.translations array Category translations
     * @responseField data.translations[].id integer Translation ID
     * @responseField data.translations[].category_id integer Foreign key reference to category
     * @responseField data.translations[].lang_code string Language code (qq, uz, or ru)
     * @responseField data.translations[].name string Category name in specified language
     * @responseField data.translations[].created_at string Translation creation timestamp (ISO 8601 format)
     * @responseField data.translations[].updated_at string Translation last update timestamp (ISO 8601 format)
     * @responseField data.created_at string Category creation timestamp (ISO 8601 format)
     * @responseField data.updated_at string Category last update timestamp (ISO 8601 format)
     * @responseField code integer HTTP status code
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Category created successfully",
     *   "data": {
     *     "id": 4,
     *     "translations": [
     *       {
     *         "id": 10,
     *         "category_id": 4,
     *         "lang_code": "qq",
     *         "name": "Pizza",
     *         "created_at": "2024-01-15T11:00:00.000000Z",
     *         "updated_at": "2024-01-15T11:00:00.000000Z"
     *       },
     *       {
     *         "id": 11,
     *         "category_id": 4,
     *         "lang_code": "uz",
     *         "name": "Pitsa",
     *         "created_at": "2024-01-15T11:00:00.000000Z",
     *         "updated_at": "2024-01-15T11:00:00.000000Z"
     *       },
     *       {
     *         "id": 12,
     *         "category_id": 4,
     *         "lang_code": "ru",
     *         "name": "Пицца",
     *         "created_at": "2024-01-15T11:00:00.000000Z",
     *         "updated_at": "2024-01-15T11:00:00.000000Z"
     *       }
     *     ],
     *     "created_at": "2024-01-15T11:00:00.000000Z",
     *     "updated_at": "2024-01-15T11:00:00.000000Z"
     *   },
     *   "code": 201
     * }
     *
     * @response 422 {
     *   "success": false,
     *   "message": "Validation error",
     *   "errors": {
     *     "translations": ["At least one translation is required"],
     *     "translations.0.lang_code": ["The language code must be one of: qq, uz, ru"],
     *     "translations.0.name": ["The category name is required", "The category name has already been taken for this language"]
     *   }
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Failed to create category",
     *   "data": null,
     *   "code": 500
     * }
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
     * Get a specific category
     *
     * @group Category Management
     * @urlParam id integer required Category ID. Example: 1
     * 
     * @responseField success boolean Operation success status
     * @responseField message string Response message
     * @responseField data object Category with translations
     * @responseField data.id integer Category ID
     * @responseField data.translations array Category translations
     * @responseField data.translations[].id integer Translation ID
     * @responseField data.translations[].category_id integer Foreign key reference to category
     * @responseField data.translations[].lang_code string Language code (qq, uz, or ru)
     * @responseField data.translations[].name string Category name in specified language
     * @responseField data.translations[].created_at string Translation creation timestamp (ISO 8601 format)
     * @responseField data.translations[].updated_at string Translation last update timestamp (ISO 8601 format)
     * @responseField data.created_at string Category creation timestamp (ISO 8601 format)
     * @responseField data.updated_at string Category last update timestamp (ISO 8601 format)
     * @responseField code integer HTTP status code
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Category retrieved successfully",
     *   "data": {
     *     "id": 1,
     *     "translations": [
     *       {
     *         "id": 1,
     *         "category_id": 1,
     *         "lang_code": "qq",
     *         "name": "Salat",
     *         "created_at": "2024-01-15T10:30:00.000000Z",
     *         "updated_at": "2024-01-15T10:30:00.000000Z"
     *       },
     *       {
     *         "id": 2,
     *         "category_id": 1,
     *         "lang_code": "uz",
     *         "name": "Salat",
     *         "created_at": "2024-01-15T10:30:00.000000Z",
     *         "updated_at": "2024-01-15T10:30:00.000000Z"
     *       },
     *       {
     *         "id": 3,
     *         "category_id": 1,
     *         "lang_code": "ru",
     *         "name": "Салат",
     *         "created_at": "2024-01-15T10:30:00.000000Z",
     *         "updated_at": "2024-01-15T10:30:00.000000Z"
     *       }
     *     ],
     *     "created_at": "2024-01-15T10:30:00.000000Z",
     *     "updated_at": "2024-01-15T10:30:00.000000Z"
     *   },
     *   "code": 200
     * }
     *
     * @response 404 {
     *   "success": false,
     *   "message": "Category not found",
     *   "data": null,
     *   "code": 404
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Failed to retrieve category",
     *   "data": null,
     *   "code": 500
     * }
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
     * Update a category
     *
     * @group Category Management
     * @urlParam id integer required Category ID. Example: 1
     * @bodyParam translations array required Array of translation objects (minimum 1). Example: [{"lang_code": "qq", "name": "Fresh Salat"}, {"lang_code": "uz", "name": "Yangi Salat"}, {"lang_code": "ru", "name": "Свежий Салат"}]
     * @bodyParam translations[].lang_code string required Language code (qq, uz, or ru). Example: uz
     * @bodyParam translations[].name string required Updated category name in specified language (max 255 characters). Example: Yangi Salat
     * 
     * @responseField success boolean Operation success status
     * @responseField message string Response message
     * @responseField data object Updated category with translations
     * @responseField data.id integer Category ID
     * @responseField data.translations array Category translations
     * @responseField data.translations[].id integer Translation ID
     * @responseField data.translations[].category_id integer Foreign key reference to category
     * @responseField data.translations[].lang_code string Language code (qq, uz, or ru)
     * @responseField data.translations[].name string Category name in specified language
     * @responseField data.translations[].created_at string Translation creation timestamp (ISO 8601 format)
     * @responseField data.translations[].updated_at string Translation last update timestamp (ISO 8601 format)
     * @responseField data.created_at string Category creation timestamp (ISO 8601 format)
     * @responseField data.updated_at string Category last update timestamp (ISO 8601 format)
     * @responseField code integer HTTP status code
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Category updated successfully",
     *   "data": {
     *     "id": 1,
     *     "translations": [
     *       {
     *         "id": 1,
     *         "category_id": 1,
     *         "lang_code": "qq",
     *         "name": "Fresh Salat",
     *         "created_at": "2024-01-15T10:30:00.000000Z",
     *         "updated_at": "2024-01-15T12:00:00.000000Z"
     *       },
     *       {
     *         "id": 2,
     *         "category_id": 1,
     *         "lang_code": "uz",
     *         "name": "Yangi Salat",
     *         "created_at": "2024-01-15T10:30:00.000000Z",
     *         "updated_at": "2024-01-15T12:00:00.000000Z"
     *       },
     *       {
     *         "id": 3,
     *         "category_id": 1,
     *         "lang_code": "ru",
     *         "name": "Свежий Салат",
     *         "created_at": "2024-01-15T10:30:00.000000Z",
     *         "updated_at": "2024-01-15T12:00:00.000000Z"
     *       }
     *     ],
     *     "created_at": "2024-01-15T10:30:00.000000Z",
     *     "updated_at": "2024-01-15T12:00:00.000000Z"
     *   },
     *   "code": 200
     * }
     *
     * @response 404 {
     *   "success": false,
     *   "message": "Category not found",
     *   "data": null,
     *   "code": 404
     * }
     *
     * @response 422 {
     *   "success": false,
     *   "message": "Validation error",
     *   "errors": {
     *     "translations.0.name": ["The category name has already been taken for this language"]
     *   }
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Failed to update category",
     *   "data": null,
     *   "code": 500
     * }
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
     * Delete a category
     *
     * @group Category Management
     * @urlParam id integer required Category ID. Example: 1
     * 
     * @responseField success boolean Operation success status
     * @responseField message string Response message
     * @responseField data null No data returned
     * @responseField code integer HTTP status code
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Category deleted successfully",
     *   "data": null,
     *   "code": 200
     * }
     *
     * @response 404 {
     *   "success": false,
     *   "message": "Category not found",
     *   "data": null,
     *   "code": 404
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Failed to delete category",
     *   "data": null,
     *   "code": 500
     * }
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
