<?php

namespace App;

trait HasInventory
{
    public function initializeHasInventory(): void
    {
        $this->mergeCasts([
            'track_inventory' => 'boolean',
            'stock_quantity' => 'integer',
            'low_stock_threshold' => 'integer',
        ]);
    }

    public function isInStock(int $quantity = 1): bool
    {
        if (!$this->track_inventory) {
            return true;
        }
        
        return $this->stock_quantity >= $quantity;
    }

    public function isLowStock(): bool
    {
        if (!$this->track_inventory || !$this->low_stock_threshold) {
            return false;
        }
        
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function isOutOfStock(): bool
    {
        if (!$this->track_inventory) {
            return false;
        }
        
        return $this->stock_quantity <= 0;
    }

    public function decrementStock(int $quantity = 1): bool
    {
        if (!$this->track_inventory) {
            return true;
        }

        if ($this->stock_quantity < $quantity) {
            return false;
        }

        $this->decrement('stock_quantity', $quantity);
        
        // Vérifier les alertes de stock
        if ($this->isLowStock()) {
            event(new \App\Events\LowStockAlert($this));
        }
        
        if ($this->isOutOfStock()) {
            event(new \App\Events\OutOfStock($this));
        }

        return true;
    }

    public function incrementStock(int $quantity = 1): void
    {
        if ($this->track_inventory) {
            $this->increment('stock_quantity', $quantity);
        }
    }

    public function setStock(int $quantity): void
    {
        $oldQuantity = $this->stock_quantity;
        
        $this->update([
            'stock_quantity' => $quantity,
            'track_inventory' => true,
        ]);

        // Log du changement de stock
        \App\Models\InventoryLog::create([
            'product_id' => $this->id,
            'old_quantity' => $oldQuantity,
            'new_quantity' => $quantity,
            'type' => 'adjustment',
            'user_id' => auth()->id(),
            'notes' => 'Ajustement manuel',
        ]);
    }
}
