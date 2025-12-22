<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslations
{
    protected function getTranslation(string $attribute): ?string
    {
        $locale = App::getLocale();
        $translation = $this->translations->firstWhere('lang_code', $locale);
        return $translation ? $translation->{$attribute} : null;
    }
}
