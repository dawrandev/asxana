<?php

namespace App\Http\Requests;

use App\Rules\UniqueCategoryTranslation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'translations' => 'required|array|min:1',
            'translations.*.lang_code' => 'required|string|in:qq,uz,ru',
            'translations.*.name' => 'required|string|max:255',
        ];

        foreach ($this->input('translations', []) as $index => $translation) {
            if (isset($translation['lang_code'])) {
                $rules["translations.{$index}.name"][] = new UniqueCategoryTranslation(
                    $translation['lang_code']
                );
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'translations.required' => 'At least one translation is required',
            'translations.array' => 'Translations must be an array',
            'translations.min' => 'At least one translation is required',
            'translations.*.lang_code.required' => 'The language code is required',
            'translations.*.lang_code.in' => 'The language code must be one of: qq, uz, ru',
            'translations.*.name.required' => 'The category name is required',
            'translations.*.name.max' => 'The category name may not be greater than 255 characters',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
