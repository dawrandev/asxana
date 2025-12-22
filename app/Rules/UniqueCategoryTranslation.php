<?php

namespace App\Rules;

use App\Models\CategoryTranslation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueCategoryTranslation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    public function __construct(
        private string $langCode,
        private ?int $exceptCategoryId = null
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = CategoryTranslation::where('lang_code', $this->langCode)
            ->where('name', $value);

        if ($this->exceptCategoryId) {
            $query->where('category_id', '!=', $this->exceptCategoryId);
        }

        if ($query->exists()) {
            $fail("This name already exists in {$this->langCode} language.");
        }
    }
}
