<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Menu extends Model
{
    use HasFactory;

    protected $table = "menus";

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'menu_categories')
            ->withPivot('position')
            ->orderByPivot('position');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'menu_products')
            ->withPivot(['position', 'custom_price'])
            ->orderByPivot('position');
    }
}
