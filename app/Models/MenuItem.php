<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'product_id',
        'special_price',
        'sort_order',
    ];

    protected $casts = [
        'special_price' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    // Relations
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Accesseurs
    public function getFormattedSpecialPriceAttribute(): ?string
    {
        return $this->special_price ? \App\Helpers\CameroonHelper::formatCurrency($this->special_price) : null;
    }

    public function getDiscountPercentageAttribute(): ?float
    {
        if ($this->special_price && $this->product) {
            return round((1 - $this->special_price / $this->product->price) * 100);
        }
        return null;
    }
}