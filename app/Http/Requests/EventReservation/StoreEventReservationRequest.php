<?php

namespace App\Http\Requests\EventReservation;

use App\Enums\EventType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventReservationRequest extends FormRequest
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
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email',
            'phone'        => 'required|string',
            'event_date'   => 'required|date|after_or_equal:today',
            'event_time'   => 'required|date_format:H:i',
            'guests_count' => 'required|integer|min:1|max:500',
            'event_type'   => [
                'required',
                'string',
                Rule::enum(EventType::class),
            ],
            'notes'        => 'nullable|string',
        ];
    }
}
