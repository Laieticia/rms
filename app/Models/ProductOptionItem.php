<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_option_id', 'name', 'price', 'sort_order', 'is_available'
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function productOption()
    {
        return $this->belongsTo(ProductOption::class);
    }

    public function orderItemOptions()
    {
        return $this->hasMany(OrderItemOption::class);
    }
}