<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;

class UpdateCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_category_translations(): void
    {
        // Create category with initial translations
        $category = Category::create();
        $category->translations()->create(['lang_code' => 'kk', 'name' => 'old1']);
        $category->translations()->create(['lang_code' => 'uz', 'name' => 'old2']);
        $category->translations()->create(['lang_code' => 'ru', 'name' => 'old3']);

        $payload = [
            'translations' => [
                ['lang_code' => 'kk', 'name' => 'shaylar'],
                ['lang_code' => 'uz', 'name' => 'choylar'],
                ['lang_code' => 'ru', 'name' => 'чай'],
            ]
        ];

        $response = $this->json('PUT', "/api/v1/categories/{$category->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'shaylar'])
            ->assertJsonFragment(['name' => 'choylar'])
            ->assertJsonFragment(['name' => 'чай']);

        $this->assertDatabaseHas('category_translations', [
            'category_id' => $category->id,
            'lang_code' => 'kk',
            'name' => 'shaylar',
        ]);
    }
}
