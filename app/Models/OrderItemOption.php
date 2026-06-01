<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id', 'option_name', 'item_name', 'price', 'quantity'
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}