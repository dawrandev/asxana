<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryService
{
    public function __construct(protected CategoryRepository $categoryRepository) {}

    public function createCategory(array $data): Category
    {
        try {
            return DB::transaction(function () use ($data) {
                $category = Category::create();

                foreach ($data['translations'] as $translation) {
                    $category->translations()->create([
                        'lang_code' => $translation['lang_code'],
                        'name' => $translation['name']
                    ]);
                }

                return $category->load('translations');
            });
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());

            throw $e;
        }
    }

    public function getCategoryById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        try {
            DB::transaction(function () use ($category, $data) {
                $category->translations()->delete();

                foreach ($data['translations'] as $translation) {
                    $category->translations()->create([
                        'lang_code' => $translation['lang_code'],
                        'name' => $translation['name']
                    ]);
                }
            });

            $category->touch();

            return $category->load('translations');
        } catch (\Exception $e) {
            Log::error('Error updating category: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteCategory(Category $category): bool
    {
        try {
            return DB::transaction(function () use ($category) {
                $category->translations()->delete();
                return $category->delete();
            });
        } catch (\Exception $e) {
            Log::error('Error deleting category: ' . $e->getMessage());

            throw $e;
        }
    }
}
