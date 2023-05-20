<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 
            'name' => $this->name,
            'discount_type' => $this->discount_type, 
            'value' => $this->value, 
            'validity' => $this->validity, 
            'limit_per_coupon' => $this->limit_per_coupon, 
            'limit_per_user' => $this->limit_per_user, 
            'is_active' => $this->is_active
        ];
    }
}
