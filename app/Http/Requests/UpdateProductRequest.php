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

    public function rules(): array
    {
        // route('id') orqali ID ni olish ishonchliroq
        $productId = $this->route('id');

        $rules = [
            'category_id'    => ['sometimes', 'exists:categories,id'],
            'image'          => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'price'          => ['sometimes', 'integer', 'min:0'],
            'is_available'   => ['sometimes', 'boolean'],
            'name'           => ['required', 'array', 'min:1'],

            // Qoidalarni massiv ko'rinishida yozamiz
            'name.kk'        => ['required', 'string', 'max:255'],
            'name.uz'        => ['required', 'string', 'max:255'],
            'name.ru'        => ['required', 'string', 'max:255'],

            'description'    => ['nullable', 'array'],
            'description.kk' => ['nullable', 'string'],
            'description.uz' => ['nullable', 'string'],
            'description.ru' => ['nullable', 'string'],
        ];

        // Unikalikni tekshirish (joriy mahsulot ID sini istisno qilgan holda)
        foreach ($this->input('name', []) as $lang => $value) {
            if (in_array($lang, ['kk', 'uz', 'ru']) && $value) {
                $rules["name.$lang"][] = new UniqueProductTranslation($lang, $productId);
            }
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        // Agar name yoki description string (JSON) bo'lib kelsa, massivga aylantiramiz
        // form-data orqali name[uz] ko'rinishida yuborilsa, bu qism kerak bo'lmaydi, 
        // lekin ehtiyot shart qoldiramiz.
        foreach (['name', 'description'] as $field) {
            if (is_string($this->input($field))) {
                $decoded = json_decode($this->input($field), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->merge([$field => $decoded]);
                }
            }
        }
    }

    public function messages(): array
    {
        return [
            'name.*.required' => 'Mahsulot nomi kiritilishi shart',
            'name.*.max' => 'Mahsulot nomi 255 belgidan ko‘p bo‘lishi mumkin emas',
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
