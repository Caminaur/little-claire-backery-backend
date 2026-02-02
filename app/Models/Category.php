<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'image_url', 'is_visible', 'position'];

    protected $casts = [
        'is_visible' => 'boolean'
    ];
}
