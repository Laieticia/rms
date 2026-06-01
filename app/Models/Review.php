<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'restaurant_id', 'order_id', 'product_id', 'rating',
        'food_rating', 'delivery_rating', 'service_rating', 'comment',
        'images', 'is_approved', 'is_featured', 'admin_response',
        'responded_at', 'helpful_count', 'unhelpful_count'
    ];

    protected $casts = [
        'images' => 'array',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'responded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function votes()
    {
        return $this->hasMany(ReviewVote::class);
    }
}