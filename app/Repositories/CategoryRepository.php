<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function getAllCategories()
    {
        return Category::all();
    }

    public function getCategoryByIdWithTranslations(int $id)
    {
        return Category::with('translations')->find($id);
    }
}
