<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductFilterRequest extends FormRequest
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
            'category_id' => 'nullable|integer|exists:categories,id',
            'is_available' => 'nullable|boolean',
            'search' => 'nullable|string|max:255',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'sort_by' => 'nullable|string|in:created_at,price,category_name',
            'sort_order' => 'nullable|string|in:asc,desc',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'Selected category does not exist',
            'is_available.boolean' => 'Is available must be true or false',
            'min_price.numeric' => 'Minimum price must be a number',
            'min_price.min' => 'Minimum price cannot be negative',
            'max_price.numeric' => 'Maximum price must be a number',
            'max_price.min' => 'Maximum price cannot be negative',
            'max_price.gte' => 'Maximum price must be greater than or equal to minimum price',
            'sort_by.in' => 'Sort by must be: created_at, price, or category_name',
            'sort_order.in' => 'Sort order must be: asc or desc',
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
