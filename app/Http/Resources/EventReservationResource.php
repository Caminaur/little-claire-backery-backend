<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'event_date'   => $this->event_date?->format('Y-m-d'),
            'event_time'   => $this->event_time,
            'guests_count' => $this->guests_count,
            'event_type'   => $this->event_type?->value,
            'notes'        => $this->notes,
            'is_read'      => $this->is_read,
            'created_at'   => $this->created_at,
        ];
    }
}
