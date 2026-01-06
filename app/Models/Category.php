<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="Category",
 *   title="Category",
 *   required={"id","name"},
 *   @OA\Property(property="id", type="integer", format="int64"),
 *   @OA\Property(property="name", type="string"),
 * )
 */

class Category extends Model
{
    use HasTranslations;

    protected $fillable = ['id'];
    protected $appends = ['name'];

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getNameAttribute(): ?string
    {
        return $this->getTranslation('name');
    }
}
