<?php

namespace App\Http\Requests;

use App\Rules\UniqueCategoryTranslation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCategoryRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $translations = $this->input('translations', null);
        if (is_string($translations)) {
            $decoded = json_decode($translations, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->merge(['translations' => $decoded]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = $this->route('id');

        $rules = [
            'translations' => 'required|array|min:1',
            'translations.*.lang_code' => 'required|string|in:kk,uz,ru',
            'translations.*.name' => 'required|string|max:255',
        ];

        foreach ($this->input('translations', []) as $index => $translation) {
            if (isset($translation['lang_code'])) {
                // Append the unique translation rule to the per-index name rule
                $rules["translations.{$index}.name"][] = new UniqueCategoryTranslation($translation['lang_code'], $categoryId);
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'translations.required' => 'Translations are required',
            'translations.array' => 'Translations must be an array',
            'translations.min' => 'At least one translation is required',
            'translations.*.lang_code.required' => 'Language code is required',
            'translations.*.lang_code.in' => 'Language code must be kk, uz, or ru',
            'translations.*.name.required' => 'Category name is required',
            'translations.*.name.max' => 'Category name cannot exceed 255 characters',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ])
        );
    }
}
