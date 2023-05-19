<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'user_id' => $this->user_id, 
            // 'product_id' => $this->product_id, 
            'product' => new ProductResource($this->product), 
            // 'product_name' => $this->product->name,
            'quantity' => $this->quantity, 
            'unit_price' => $this->unit_price, 
            'sub_total' => $this->sub_total, 
        ];
    }
}
