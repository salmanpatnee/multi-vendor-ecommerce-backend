<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'brand' => new BrandResource($this->brand),
            'category' => CategoryResource::collection($this->categories),
            'user' => new UserResource($this->user),
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'qty' => $this->qty,
            'tags' => $this->tags,
            'sizes' => $this->sizes,
            'colors' => $this->colors,
            'price' => $this->price,
            'short_desc' => $this->short_desc,
            'desc' => $this->desc,
            'image' => $this->image,
            'is_hot' => $this->is_hot,
            'is_featured' => $this->is_featured,
            'is_offer' => $this->is_offer,
            'is_deal' => $this->is_deal,
            'is_active' => $this->is_active,
        ];
    }
}
