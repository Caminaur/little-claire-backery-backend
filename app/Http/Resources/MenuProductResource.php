<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'position' => $this->pivot->position,
            'variants' => $this->variants->map(fn($v) => [
                'id' => $v->id,
                'label' => $v->label,
                'price' => $v->price,
                'position' => $v->position,
                'is_active' => $v->is_active,
            ]),
        ];
    }
}
