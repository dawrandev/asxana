<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $names = [];

        if ($this->relationLoaded('translations')) {
            foreach ($this->translations as $translation) {
                $names[$translation->lang_code] = $translation->name;
            }
        } else {
            $names = $this->name;
        }

        return [
            'id' => $this->id,
            'name' => $names, // Natija: {"uz": "...", "ru": "...", "kk": "..."}
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
