<?php

namespace App\Http\Requests;

use App\Rules\UniqueProductTranslation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreProductRequest extends FormRequest
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
        return [
            'category_id' => 'required|integer|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|integer|min:0',
            'is_available' => 'sometimes|boolean',

            'translations' => 'required|array|min:1',
            'translations.*.lang_code' => 'required|string|in:qq,uz,ru',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
        ];

        foreach ($this->input('translations', []) as $index => $translation) {
            if (isset($translation['lang_code'])) {
                $rules["translations.{$index}.name"][] = new UniqueProductTranslation(
                    $translation['lang_code']
                );
            }
        }
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategoriya tanlanishi shart',
            'category_id.exists' => 'Tanlangan kategoriya mavjud emas',
            'image.required' => 'Mahsulot rasmi yuklanishi shart',
            'price.required' => 'Mahsulot narxi kiritilishi shart',
            'translations.required' => 'Kamida bitta tilda maʼlumot boʻlishi shart',
            'translations.*.lang_code.in' => 'Til kodi notoʻgʻri (qq, uz, ru boʻlishi kerak)',
            'translations.*.name.required' => 'Mahsulot nomi kiritilishi shart',
            'translations.*.description.required' => 'Mahsulot tavsifi kiritilishi shart',
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
