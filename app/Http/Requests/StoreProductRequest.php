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
        $rules = [
            'category_id' => 'required|integer|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|integer|min:0',
            'is_available' => 'sometimes|boolean',

            'name' => 'required|array|min:1',
            'name.kk' => ['required', 'string', 'max:255'],
            'name.uz' => ['required', 'string', 'max:255'],
            'name.ru' => ['required', 'string', 'max:255'],

            'description' => 'nullable|array',
            'description.kk' => ['nullable', 'string'],
            'description.uz' => ['nullable', 'string'],
            'description.ru' => ['nullable', 'string'],
        ];

        foreach ($this->input('name', []) as $lang => $value) {
            if (in_array($lang, ['kk', 'uz', 'ru'])) {
                $rules["name.$lang"][] = new UniqueProductTranslation($lang);
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategoriya tanlanishi shart',
            'category_id.exists' => 'Tanlangan kategoriya mavjud emas',
            'image.required' => 'Mahsulot rasmi yuklanishi shart',
            'price.required' => 'Mahsulot narxi kiritilishi shart',
            'name.required' => 'Kamida bitta tilda maʼlumot boʻlishi shart',
            'name.*.required' => 'Mahsulot nomi kiritilishi shart',
            'name.*.max' => 'Mahsulot nomi 255 belgidan ko‘p bo‘lishi mumkin emas',
            'description.*.string' => 'Tavsif matn formatida bo‘lishi kerak',
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
