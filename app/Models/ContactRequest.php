<?php

namespace App\Models;

use App\Enums\ContactRequestType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    use HasFactory;

    protected $table = "contact_requests";

    protected $attributes = [
        'is_read' => false,
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'type',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'type' => ContactRequestType::class
    ];
}
