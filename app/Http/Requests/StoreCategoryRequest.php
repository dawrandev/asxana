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
            'name' => 'required|array|min:1',
            'name.kk' => ['required', 'string', 'max:255'],
            'name.uz' => ['required', 'string', 'max:255'],
            'name.ru' => ['required', 'string', 'max:255'],
        ];

        foreach ($this->input('name', []) as $lang => $value) {
            if (in_array($lang, ['kk', 'uz', 'ru'])) {
                $rules["name.$lang"][] =
                    new UniqueCategoryTranslation($lang);
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
            'translations.*.lang_code.in' => 'The language code must be one of: kk, uz, ru',
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
