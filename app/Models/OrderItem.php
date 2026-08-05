<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'unit_price',
        'quantity',
        'total_price',
        'special_instructions',
        'product_data',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity' => 'integer',
        'product_data' => 'array',
    ];

    protected $appends = [
        'formatted_unit_price',
        'formatted_total_price',
    ];

    // Relations
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(OrderItemOption::class);
    }

    // Accesseurs
    public function getFormattedUnitPriceAttribute(): string
    {
        return \App\Helpers\CameroonHelper::formatCurrency($this->unit_price);
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return \App\Helpers\CameroonHelper::formatCurrency($this->total_price);
    }

    // Méthodes
    public function calculateTotal(): void
    {
        $optionsTotal = $this->options->sum(function($option) {
            return $option->price * $option->quantity;
        });
        
        $this->total_price = ($this->unit_price * $this->quantity) + $optionsTotal;
        $this->save();
    }

    public function saveProductSnapshot(): void
    {
        $product = $this->product;
        $this->product_data = [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'category' => $product->category->name ?? null,
            'allergens' => $product->allergens,
            'image' => $product->primary_image_url,
        ];
        $this->save();
    }
}