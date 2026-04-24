<?php

namespace App\Models;

use App\Enums\EventType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventReservation extends Model
{
    use HasFactory;

    protected $table = 'event_reservations';

    protected $attributes = [
        'is_read' => false,
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'event_date',
        'event_time',
        'guests_count',
        'event_type',
        'notes',
        'is_read',
    ];

    protected $casts = [
        'event_type' => EventType::class,
        'is_read'    => 'boolean',
        'event_date' => 'date',
    ];
}
