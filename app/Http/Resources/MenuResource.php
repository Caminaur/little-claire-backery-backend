<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\File;

class MenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $pdfPath = storage_path("app/public/menus/menu-{$this->id}.pdf");

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'is_active'   => $this->is_active,
            'pdf_url'     => File::exists($pdfPath) ? asset("storage/menus/menu-{$this->id}.pdf") : null,
        ];
    }
}
