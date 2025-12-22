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
use PDO;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService,
        protected CategoryRepository $categoryRepository
    ) {}

    public function index(): Jsonresponse
    {
        try {
            $categories = $this->categoryRepository->getAllCategories();

            $data = CategoryResource::collection($categories);

            return $this->jsonResponse(true, 'Categories retrieved successfully', $data, 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Failed to retrieve categories', null, 500);
        }
    }

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

    public function update(UpdateCategoryRequest $request, string $id)
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

    public function jsonResponse($status, $message, $data, $code)
    {
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data'    => $data,
            'code'    => $code
        ], $code);
    }
}
