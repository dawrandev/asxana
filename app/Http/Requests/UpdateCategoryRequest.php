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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = $this->route('id');

        $rules = [
            'name' => 'required|array|min:1',
            'name.kk' => ['required', 'string', 'max:255'],
            'name.uz' => ['required', 'string', 'max:255'],
            'name.ru' => ['required', 'string', 'max:255'],
        ];

        foreach ($this->input('name', []) as $lang => $value) {
            if (in_array($lang, ['kk', 'uz', 'ru'])) {
                $rules["name.$lang"][] =
                    new UniqueCategoryTranslation($lang, $categoryId);
            }
        }

        return $rules;
    }


    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required',
            'name.array' => 'Name must be an object',
            'name.min' => 'At least one language is required',
            'name.*.required' => 'Category name is required',
            'name.*.max' => 'Category name cannot exceed 255 characters',
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
