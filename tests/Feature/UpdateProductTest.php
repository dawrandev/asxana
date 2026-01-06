<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use App\Models\Product;
use App\Models\Category;

class UpdateProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_product_with_multipart_and_same_translation_names_allowed(): void
    {
        $category = Category::create();

        $product = Product::create([
            'category_id' => $category->id,
            'image' => 'products/old.jpg',
            'price' => 100,
            'is_available' => true,
        ]);

        $product->translations()->create(['lang_code' => 'kk', 'name' => 'old1', 'description' => 'desc']);
        $product->translations()->create(['lang_code' => 'uz', 'name' => 'old2', 'description' => 'desc']);
        $product->translations()->create(['lang_code' => 'ru', 'name' => 'old3', 'description' => 'desc']);

        $payload = [
            '_method' => 'PUT',
            'price' => 150,
            'translations' => [
                ['lang_code' => 'kk', 'name' => 'old1', 'description' => 'desc2'],
                ['lang_code' => 'uz', 'name' => 'old2', 'description' => 'desc2'],
                ['lang_code' => 'ru', 'name' => 'old3', 'description' => 'desc2'],
            ],
            'image' => UploadedFile::fake()->image('new.jpg'),
        ];

        $response = $this->post("/api/v1/products/{$product->id}", $payload, ['Accept' => 'application/json']);

        // Expect success and ensure no uniqueness validation error for the same product
        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Product updated successfully']);

        $this->assertDatabaseHas('product_translations', [
            'product_id' => $product->id,
            'lang_code' => 'uz',
            'name' => 'old2',
        ]);
    }
}
