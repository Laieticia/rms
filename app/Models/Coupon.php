<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id', 'code', 'description', 'type', 'value',
        'min_order_amount', 'max_discount_amount', 'max_uses',
        'used_count', 'max_uses_per_user', 'is_active', 'starts_at',
        'expires_at', 'applies_to_all'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'applies_to_all' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'coupon_product');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'coupon_category');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}