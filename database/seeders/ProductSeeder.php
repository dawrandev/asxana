<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'category_id' => 1,
                'price' => 25000,
                'is_available' => 1,
                'image' => 'sezar-salat.jpg',
                'translations' => [
                    [
                        'lang_code' => 'qq',
                        'name' => 'Sezar salat',
                        'description' => 'Tovuq go\'shti, parmezanli klassik Sezar salat'
                    ],
                    [
                        'lang_code' => 'uz',
                        'name' => 'Sezar salat',
                        'description' => 'Tovuq go\'shti, parmezan bilan klassik Sezar salat'
                    ],
                    [
                        'lang_code' => 'ru',
                        'name' => 'Салат Цезарь',
                        'description' => 'Классический салат Цезарь с курицей и пармезаном'
                    ],
                ],
            ],
            [
                'category_id' => 1, // Salat
                'price' => 22000,
                'is_available' => 1,
                'image' => 'greek-salat.jpg',
                'translations' => [
                    [
                        'lang_code' => 'qq',
                        'name' => 'Grek salat',
                        'description' => 'Feta pınır, zeytun, pomidor, qıyar salat'
                    ],
                    [
                        'lang_code' => 'uz',
                        'name' => 'Grek salat',
                        'description' => 'Feta pishloq, zaytun, pomidor, bodring salat'
                    ],
                    [
                        'lang_code' => 'ru',
                        'name' => 'Греческий салат',
                        'description' => 'Салат с сыром фета, оливками, помидорами и огурцами'
                    ],
                ],
            ],
            [
                'category_id' => 2, // Hot-dog
                'price' => 18000,
                'is_available' => 1,
                'image' => 'klassik-hot-dog.jpg',
                'translations' => [
                    [
                        'lang_code' => 'qq',
                        'name' => 'Klassik Hot-dog',
                        'description' => 'Sosiska, xıyar turşısı, ketçup, gorçitsa'
                    ],
                    [
                        'lang_code' => 'uz',
                        'name' => 'Klassik Hot-dog',
                        'description' => 'Sosiska, tuzlangan bodring, ketchup, xanal'
                    ],
                    [
                        'lang_code' => 'ru',
                        'name' => 'Классический Хот-дог',
                        'description' => 'Сосиска, маринованный огурец, кетчуп, горчица'
                    ],
                ],
            ],
            [
                'category_id' => 3, // Burger
                'price' => 35000,
                'is_available' => 1,
                'image' => 'chizburger.jpg',
                'translations' => [
                    [
                        'lang_code' => 'qq',
                        'name' => 'Chizburger',
                        'description' => 'Mol go\'shti kotlet, cheddar pınır, pomidor, salat'
                    ],
                    [
                        'lang_code' => 'uz',
                        'name' => 'Chizburger',
                        'description' => 'Mol go\'shti kotlet, cheddar pishloq, pomidor, salat'
                    ],
                    [
                        'lang_code' => 'ru',
                        'name' => 'Чизбургер',
                        'description' => 'Котлета из говядины, сыр чеддер, помидор, салат'
                    ],
                ],
            ],
            [
                'category_id' => 3, // Burger
                'price' => 42000,
                'is_available' => false,
                'image' => 'bekın-burger.jpg',
                'translations' => [
                    [
                        'lang_code' => 'qq',
                        'name' => 'Bekın Burger',
                        'description' => 'Qızarıtılgan bekın, mol go\'shti, BBQ sous'
                    ],
                    [
                        'lang_code' => 'uz',
                        'name' => 'Bekon Burger',
                        'description' => 'Qovurilgan bekon, mol go\'shti, BBQ sous'
                    ],
                    [
                        'lang_code' => 'ru',
                        'name' => 'Бургер с беконом',
                        'description' => 'Жареный бекон, говядина, соус BBQ'
                    ],
                ],
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create([
                'category_id' => $productData['category_id'],
                'price' => $productData['price'],
                'is_available' => $productData['is_available'],
                'image' => $productData['image'],
            ]);

            foreach ($productData['translations'] as $translationData) {
                $product->translations()->create($translationData);
            }
        }
    }
}
