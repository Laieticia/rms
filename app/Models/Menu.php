<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id', 'name', 'slug', 'description', 'type',
        'start_date', 'end_date', 'available_from', 'available_until',
        'is_active', 'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'menu_items')
                    ->withPivot('special_price', 'sort_order')
                    ->withTimestamps();
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}