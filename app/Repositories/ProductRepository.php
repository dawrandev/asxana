<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductRepository
{
    public function getProducts(array $filters): Collection
    {
        $query = Product::with('translations', 'category');

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['is_aviable'])) {
            $query->where('is_aviable', $filters['is_aviable']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->whereHas('translations', function ($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', "%{$search}%");
                    $subQ->orWhere('description', 'LIKE', "%{$search}%");
                })

                    ->orWhereHas('category.translations', function ($subQ) use ($search) {
                        $subQ->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        if (!empty($filters['min_price']) && !empty($filters['max_price'])) {
            $query->whereBetween('price', [$filters['min_price'], $filters['max_price']]);
        }

        return $query->get();
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

            $product->translations()->delete();
            foreach ($translations as $translation) {
                $product->translations()->create($translation);
            }

            $product->touch();
            return $product->load('translations', 'category');
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
