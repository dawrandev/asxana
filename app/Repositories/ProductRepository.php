<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductRepository
{
    public function getProducts(array $filters): LengthAwarePaginator
    {
        $query = Product::with('translations', 'category');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('translations', function ($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('category.translations', function ($subQ) use ($search) {
                        $subQ->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    public function getProductById(int $id): ?Product
    {
        return Product::with('translations', 'category')->find($id);
    }

    public function store(array $productData, array $translations): Product
    {
        return DB::transaction(function () use ($productData, $translations) {
            $product = Product::create($productData);

            foreach ($translations as $translation) {
                $product->translations()->create([
                    'lang_code'   => $translation['lang_code'],
                    'name'        => $translation['name'],
                    'description' => $translation['description'],
                ]);
            }

            return $product->load('translations', 'category');
        });
    }

    public function update(Product $product, array $productData, array $translations): Product
    {
        return DB::transaction(function () use ($product, $productData, $translations) {
            $product->update($productData);

            if (!empty($translations)) {
                $product->translations()->delete();

                foreach ($translations as $translation) {
                    $product->translations()->create([
                        'lang_code' => $translation['lang_code'],
                        'name' => $translation['name'],
                        'description' => $translation['description'],
                    ]);
                }
            }

            return $product->fresh(['translations', 'category']);
        });
    }

    public function delete(Product $product): void
    {
        DB::transaction(function () use ($product) {
            $product->translations()->delete();
            $product->delete();
        });
    }
}
