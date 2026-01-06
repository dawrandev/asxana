<?php

namespace App\Http\Requests;

use App\Rules\UniqueProductTranslation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('id') !== null ? (int) $this->route('id') : null;

        $rules = [
            'category_id'  => 'sometimes|exists:categories,id',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'price'        => 'sometimes|integer|min:0',
            'is_available' => 'sometimes|boolean',

            'translations'                => 'required|array|min:1',
            'translations.*.lang_code'    => 'required|string|in:qq,uz,ru',
            'translations.*.name'         => 'required|string|max:255',
            'translations.*.description'  => 'required|string',
        ];

        foreach ($this->input('translations', []) as $index => $translation) {
            if (isset($translation['lang_code'])) {
                $rules["translations.{$index}.name"][] = new UniqueProductTranslation(
                    $translation['lang_code'],
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
            'image.image' => 'Yuklangan fayl rasm bo\'lishi kerak',
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
