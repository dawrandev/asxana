<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="ProductResource",
 * title="Product Resource",
 * description="Mahsulot ma'lumotlari modeli",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="category_name", type="string", example="Salat"),
 * @OA\Property(property="image", type="string", example="https://misol.uz/storage/products/rasm.jpg"),
 * @OA\Property(property="price", type="integer", example=25000),
 * @OA\Property(property="name", type="string", example="Sezar salat"),
 * @OA\Property(property="description", type="string", example="Tovuqli klassik salat"),
 * @OA\Property(property="is_available", type="boolean", example=true),
 * @OA\Property(property="created_at", type="string", format="date-time"),
 * @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_name' => $this->category->name,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'price' => $this->price,
            'name' => $this->name,
            'description' => $this->description,
            'is_available' => (bool)$this->is_available, // Frontendga boolean tipida borishi uchun
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
