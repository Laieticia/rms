<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Menu extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'restaurant_id',
        'name',
        'slug',
        'description',
        'type',
        'start_date',
        'end_date',
        'available_from',
        'available_until',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
    ];

    protected $appends = ['is_available', 'products_count'];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    // Relations
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'menu_items')
            ->withPivot('special_price', 'sort_order')
            ->withTimestamps()
            ->orderBy('menu_items.sort_order');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }

    // Accesseurs
    public function getIsAvailableAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        // Vérifier la date de début
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        // Vérifier la date de fin
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        // Vérifier les heures de disponibilité
        if ($this->available_from || $this->available_until) {
            $currentTime = $now->format('H:i:s');
            
            if ($this->available_from && $currentTime < $this->available_from) {
                return false;
            }
            
            if ($this->available_until && $currentTime > $this->available_until) {
                return false;
            }
        }

        return true;
    }

    public function getProductsCountAttribute(): int
    {
        return $this->products()->count();
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'regular' => 'Menu Régulier',
            'lunch' => 'Menu Déjeuner',
            'dinner' => 'Menu Dîner',
            'weekend' => 'Menu Weekend',
            'special' => 'Menu Spécial',
            'seasonal' => 'Menu Saisonnier',
            default => 'Menu',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'regular' => 'primary',
            'lunch' => 'warning',
            'dinner' => 'info',
            'weekend' => 'success',
            'special' => 'danger',
            'seasonal' => 'secondary',
            default => 'secondary',
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        $now = now();
        $currentTime = $now->format('H:i:s');

        return $query->where('is_active', true)
            ->where(function($q) use ($now) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $now);
            })
            ->where(function($q) use ($currentTime) {
                $q->whereNull('available_from')
                  ->orWhere('available_from', '<=', $currentTime);
            })
            ->where(function($q) use ($currentTime) {
                $q->whereNull('available_until')
                  ->orWhere('available_until', '>=', $currentTime);
            });
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeLunch($query)
    {
        return $query->where('type', 'lunch');
    }

    public function scopeDinner($query)
    {
        return $query->where('type', 'dinner');
    }

    public function scopeWeekend($query)
    {
        return $query->where('type', 'weekend');
    }

    public function scopeSpecial($query)
    {
        return $query->where('type', 'special');
    }

    // Méthodes
    public function isAvailable(): bool
    {
        return $this->getIsAvailableAttribute();
    }

    public function addProduct(Product $product, ?float $specialPrice = null, ?int $sortOrder = null): void
    {
        $this->products()->attach($product->id, [
            'special_price' => $specialPrice,
            'sort_order' => $sortOrder ?? $this->products()->count(),
        ]);
    }

    public function removeProduct(Product $product): void
    {
        $this->products()->detach($product->id);
    }

    public function updateProductPrice(Product $product, float $specialPrice): void
    {
        $this->products()->updateExistingPivot($product->id, [
            'special_price' => $specialPrice,
        ]);
    }

    public function getDiscountedProducts(): array
    {
        return $this->products()
            ->wherePivotNotNull('special_price')
            ->get()
            ->map(function($product) {
                return [
                    'product' => $product,
                    'original_price' => $product->price,
                    'special_price' => $product->pivot->special_price,
                    'discount_percentage' => round((1 - $product->pivot->special_price / $product->price) * 100),
                ];
            })
            ->toArray();
    }

    public function getTotalOriginalPrice(): float
    {
        return $this->products()->sum('price');
    }

    public function getTotalSpecialPrice(): float
    {
        $total = 0;
        
        $this->products()->get()->each(function($product) use (&$total) {
            $total += $product->pivot->special_price ?? $product->price;
        });
        
        return $total;
    }

    public function getSavings(): float
    {
        return $this->getTotalOriginalPrice() - $this->getTotalSpecialPrice();
    }
}