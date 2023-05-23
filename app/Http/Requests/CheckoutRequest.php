<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
            'billing_name' => 'required|string', 
            'billing_email' => 'required|email', 
            'billing_phone' => 'required|string', 
            'zip_code' => 'required|string', 
            'division_id' => 'required|integer|exists:divisions,id', 
            'district_id' => 'required|integer|exists:districts,id', 
            'shipping_area_id' => 'required|integer|exists:shipping_areas,id', 
            'shipping_address' => 'required|string', 
            'notes' => 'nullable|string', 
            'total' => 'required|numeric', 
            'payment_method' => 'required|string'
        ];
    }
}
