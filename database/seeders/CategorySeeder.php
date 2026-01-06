<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'translations' => [
                    ['lang_code' => 'kk', 'name' => 'Salat'],
                    ['lang_code' => 'uz', 'name' => 'Salat'],
                    ['lang_code' => 'ru', 'name' => 'Салат'],
                ],
            ],
            [
                'translations' => [
                    ['lang_code' => 'kk', 'name' => 'Hot-dog'],
                    ['lang_code' => 'uz', 'name' => 'Hot-dog'],
                    ['lang_code' => 'ru', 'name' => 'Хот-дог'],
                ],
            ],
            [
                'translations' => [
                    ['lang_code' => 'kk', 'name' => 'Burger'],
                    ['lang_code' => 'uz', 'name' => 'Burger'],
                    ['lang_code' => 'ru', 'name' => 'Бургер'],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = \App\Models\Category::create();
            foreach ($categoryData['translations'] as $translationData) {
                $category->translations()->create($translationData);
            }
        }
    }
}
