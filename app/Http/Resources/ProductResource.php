<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'image' => $this->image,
            'price' => $this->price,
            'name' => $this->name,
            'description' => $this->description,
            'is_aviable' => $this->is_aviable,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
