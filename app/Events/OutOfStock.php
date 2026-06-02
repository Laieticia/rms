<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OutOfStock
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;
    public $restaurant;

    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->restaurant = $product->restaurant;
    }

    public function getMessage(): string
    {
        return "Rupture de stock pour {$this->product->name}";
    }
}