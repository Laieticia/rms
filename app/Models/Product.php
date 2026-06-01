<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'restaurant_id', 'category_id', 'name', 'slug', 'description',
        'price', 'compare_price', 'cost_price', 'sku', 'barcode',
        'preparation_time', 'calories', 'is_vegetarian', 'is_vegan',
        'is_gluten_free', 'is_spicy', 'allergens', 'nutritional_info',
        'is_available', 'is_featured', 'sort_order', 'stock_quantity',
        'track_inventory', 'low_stock_threshold', 'weight', 'unit',
        'max_per_order', 'min_per_order', 'tags', 'rating_avg',
        'rating_count', 'orders_count', 'views_count'
    ];

    protected $casts = [
        'allergens' => 'array',
        'nutritional_info' => 'array',
        'tags' => 'array',
        'is_vegetarian' => 'boolean',
        'is_vegan' => 'boolean',
        'is_gluten_free' => 'boolean',
        'is_spicy' => 'boolean',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'track_inventory' => 'boolean',
        'rating_avg' => 'decimal:2',
    ];

    // Relations
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_items');
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_product');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
}