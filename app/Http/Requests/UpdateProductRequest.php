<?php

namespace App\Http\Requests;

use App\Rules\UniqueProductTranslation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $productId = $this->route('id') !== null ? (int) $this->route('id') : null;

        $this->merge([
            'product_id' => $productId,
        ]);

        if (is_string($this->input('translations'))) {
            $decoded = json_decode($this->input('translations'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge(['translations' => $decoded]);
            } else {
                Log::error('JSON decode error:', ['error' => json_last_error_msg()]);
            }
        }
    }

    public function rules(): array
    {
        $productId = $this->input('product_id');

        $rules = [
            'category_id'  => 'sometimes|exists:categories,id',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'price'        => 'sometimes|integer|min:0',
            'is_available' => 'sometimes|boolean',
            'translations' => 'required|array|min:1',
            'translations.*.lang_code' => 'required|string|in:kk,uz,ru',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
        ];

        $langCodes = [];
        foreach ($this->input('translations', []) as $index => $translation) {
            if (isset($translation['lang_code']) && isset($translation['name'])) {
                $langCode = $translation['lang_code'];
                $name = $translation['name'];

                if (in_array($langCode, $langCodes)) {
                    continue;
                }
                $langCodes[] = $langCode;

                $rules["translations.{$index}.name"][] = new UniqueProductTranslation(
                    $langCode,
                    $productId
                );
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'translations.*.name.required' => 'Mahsulot nomi kiritilishi shart',
            'translations.*.lang_code.required' => 'Til kodi kiritilishi shart',
            'translations.*.lang_code.in' => 'Til kodi kk, uz yoki ru bo\'lishi kerak',
            'translations.*.description.required' => 'Mahsulot tavsifi kiritilishi shart',
            'image.image' => 'Yuklangan fayl rasm bo\'lishi kerak',
            'image.mimes' => 'Rasm jpeg, png yoki jpg formatida bo\'lishi kerak',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        Log::error('Validation failed:', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->all()
        ]);

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422)
        );
    }

    public function passedValidation()
    {
        // No-op after validation
    }
}
