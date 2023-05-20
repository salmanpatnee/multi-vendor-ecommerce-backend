<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponUpdateRequest extends FormRequest
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
            'name' => 'required|string|unique:coupons,name,'. $this->coupon->id, 
            'discount_type' => 'required|string|in:Fixed,Percentage', 
            'value' => 'required|numeric|gt:0', 
            'validity' => 'required|date|after:today', 
            'limit_per_coupon' => 'nullable|integer|gt:0',
            'limit_per_user' => 'nullable|integer|gt:0', 
            'is_active' => 'nullable|boolean'
        ];
    }
}
