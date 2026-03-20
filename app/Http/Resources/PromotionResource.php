<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'description'    => $this->description,
            'discount_type'  => $this->discount_type,
            'discount_value' => $this->discount_value,
            'starts_at'      => $this->starts_at,
            'ends_at'        => $this->ends_at,
            'is_active'      => $this->is_active,
        ];
    }
}
