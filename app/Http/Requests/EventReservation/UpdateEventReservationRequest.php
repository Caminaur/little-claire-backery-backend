<?php

namespace App\Http\Requests\EventReservation;

use App\Enums\EventType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'         => 'sometimes|required|string|max:255',
            'email'        => 'sometimes|string|email',
            'phone'        => 'sometimes|string',
            'event_date'   => 'sometimes|date|after_or_equal:today',
            'event_time'   => 'sometimes|date_format:H:i',
            'guests_count' => 'sometimes|integer|min:1|max:500',
            'event_type'   => [
                'sometimes',
                'string',
                Rule::enum(EventType::class),
            ],
            'notes'        => 'sometimes|nullable|string',
            'is_read'      => 'sometimes|boolean',
        ];
    }
}
