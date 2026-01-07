<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(protected ProductRepository $productRepository) {}

    public function getProducts(array $data)
    {
        return $this->productRepository->getProducts($data);
    }

    public function createProduct(array $data): Product
    {
        try {
            $path = $data['image']->store('products', 'public');

            $productData = [
                'category_id' => $data['category_id'],
                'image' => $path,
                'price' => $data['price'],
                'is_available' => $data['is_available'] ?? true,
            ];

            $names = $data['name'] ?? [];
            $descriptions = $data['description'] ?? [];

            $translations = collect($names)
                ->map(function ($name, $lang) use ($descriptions) {
                    return [
                        'lang_code' => $lang,
                        'name' => $name,
                        'description' => $descriptions[$lang] ?? null,
                    ];
                })
                ->values()
                ->toArray();

            return $this->productRepository->store($productData, $translations);
        } catch (\Exception $e) {
            Log::error('Error creating product: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateProduct(Product $product, array $data): Product
    {
        try {
            $productData = [
                'category_id'  => $data['category_id'] ?? $product->category_id,
                'price'        => $data['price'] ?? $product->price,
                'is_available' => $data['is_available'] ?? $product->is_available,
            ];

            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $productData['image'] = $data['image']->store('products', 'public');
            }

            $names = $data['name'] ?? [];
            $descriptions = $data['description'] ?? [];

            $translations = collect($names)
                ->map(function ($name, $lang) use ($descriptions) {
                    return [
                        'lang_code' => $lang,
                        'name' => $name,
                        'description' => $descriptions[$lang] ?? null,
                    ];
                })
                ->values()
                ->toArray();

            return $this->productRepository->update($product, $productData, $translations);
        } catch (\Exception $e) {
            Log::error('Product update failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteProduct(Product $product): void
    {
        try {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $this->productRepository->delete($product);
        } catch (\Exception $e) {
            Log::error('Product delete failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
