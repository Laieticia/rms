<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockAlert
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
        return "Stock faible pour {$this->product->name}: " . 
               "{$this->product->stock_quantity} unités restantes " .
               "(seuil: {$this->product->low_stock_threshold})";
    }

    public function getAlertLevel(): string
    {
        if ($this->product->stock_quantity === 0) {
            return 'danger';
        }
        
        $percentage = ($this->product->stock_quantity / $this->product->low_stock_threshold) * 100;
        
        if ($percentage <= 25) return 'danger';
        if ($percentage <= 50) return 'warning';
        return 'info';
    }
}