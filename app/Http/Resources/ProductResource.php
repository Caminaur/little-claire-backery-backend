<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'category_id'   => $this->category_id,
            'category_name' => $this->whenLoaded('category', fn() => $this->category?->name),
            'name'          => $this->name,
            'description'   => $this->description,
            'image_url'     => $this->image_url ? asset($this->image_url) : null,
            'is_active'     => $this->is_active,
            'variants'      => ProductVariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}
