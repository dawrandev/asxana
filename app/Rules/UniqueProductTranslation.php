<?php

namespace App\Rules;

use App\Models\ProductTranslation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueProductTranslation implements ValidationRule
{
    public function __construct(
        private string $langCode,
        private ?int $exceptProductId = null
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = ProductTranslation::where('lang_code', $this->langCode)
            ->where('name', $value);

        if ($this->exceptProductId !== null) {
            $query->where('product_id', '!=', $this->exceptProductId);
        }

        if ($query->exists()) {
            $fail("This product name already exists in {$this->langCode} language.");
        }
    }
}
