<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'brand_id' => 'nullable|integer|exists:brands,id', 
            'category_id.*' => 'nullable|integer|exists:categories,id', 
            'user_id' => 'nullable|integer|exists:users,id', 
            'name' => 'required|string', 
            'code' => 'nullable|string|unique:products,code', 
            'qty' => 'nullable|numeric|gt:-1', 
            'tags' => 'nullable|string', 
            'sizes' => 'nullable|string', 
            'colors' => 'nullable|string', 
            'price' => 'required|numeric|gt:0', 
            'sale_price' => 'nullable|numeric|lt:price', 
            'short_desc' => 'nullable|string', 
            'desc' => 'nullable|string', 
            'image' => 'nullable|image', 
            'gallery.*' => 'nullable|image', 
            'is_hot' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_offer' => 'nullable|boolean',
            'is_deal' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }
}
