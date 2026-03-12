<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'image_url', 'is_visible', 'position'];

    protected $casts = [
        'is_visible' => 'boolean'
    ];

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'menu_categories')
            ->withPivot('position')
            ->orderByPivot('position');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
