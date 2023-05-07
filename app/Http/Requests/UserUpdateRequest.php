<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'name'      => 'required|string|min:3|max:255',
            'username'     => 'required|string|max:255|unique:users,username,' . $this->user->id,
            'shop_name'     => 'required_if:role,Vendor|string|max:255',
            'email'     => 'required|email|max:255|unique:users,email,' . $this->user->id,
            'phone'     => 'nullable|string|max:255|unique:users,phone,' . $this->user->id,
            'address'     => 'nullable|string',
            'password'  => 'sometimes|min:6|max:255|confirmed',
            'is_active'  => 'nullable|boolean',
            'role'  => 'required|string|in:Administrator,Vendor,Customer',
        ];
    }
}
