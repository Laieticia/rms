<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = [
        'restaurant_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'compare_price',
        'cost_price',
        'sku',
        'barcode',
        'preparation_time',
        'calories',
        'is_vegetarian',
        'is_vegan',
        'is_gluten_free',
        'is_spicy',
        'allergens',
        'nutritional_info',
        'is_available',
        'is_featured',
        'sort_order',
        'stock_quantity',
        'track_inventory',
        'low_stock_threshold',
        'weight',
        'unit',
        'max_per_order',
        'min_per_order',
        'tags',
        'rating_avg',
        'rating_count',
        'orders_count',
        'views_count',
    ];

    protected $casts = [
        'allergens' => 'array',
        'nutritional_info' => 'array',
        'tags' => 'array',
        'is_vegetarian' => 'boolean',
        'is_vegan' => 'boolean',
        'is_gluten_free' => 'boolean',
        'is_spicy' => 'boolean',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'track_inventory' => 'boolean',
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'rating_avg' => 'decimal:2',
        'weight' => 'decimal:2',
        'preparation_time' => 'integer',
        'calories' => 'integer',
        'stock_quantity' => 'integer',
    ];

    protected $appends = [
        'primary_image_url',
        'formatted_price',
        'discount_percentage',
        'is_on_sale',
        'is_in_stock',
        'average_preparation_time',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    // Relations
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'menu_items')
            ->withPivot('special_price', 'sort_order')
            ->withTimestamps();
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_product');
    }

    public function loyaltyRewards(): HasMany
    {
        return $this->hasMany(LoyaltyReward::class, 'free_product_id');
    }

    // Accesseurs
    public function getAllergensAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return is_array($decoded) ? $decoded : (array) $decoded;
            }
            return array_filter(array_map('trim', explode(',', $value)));
        }

        return (array) $value;
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $primaryImage = $this->images->where('is_primary', true)->first() 
                       ?? $this->images->first();
        
        return $primaryImage ? asset('storage/' . $primaryImage->path) : null;
    }

    public function getFormattedPriceAttribute(): string
    {
        return \App\Helpers\CameroonHelper::formatCurrency($this->price);
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return round((1 - $this->price / $this->compare_price) * 100);
        }
        return null;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->compare_price && $this->compare_price > $this->price;
    }

    public function getIsInStockAttribute(): bool
    {
        if (!$this->track_inventory) {
            return true;
        }
        return $this->stock_quantity > 0;
    }

    public function getAveragePreparationTimeAttribute(): string
    {
        return $this->preparation_time . ' min';
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->cost_price && $this->cost_price > 0) {
            return (($this->price - $this->cost_price) / $this->price) * 100;
        }
        return 0;
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
            ->where(function($q) {
                $q->where('track_inventory', false)
                  ->orWhere(function($sq) {
                      $sq->where('track_inventory', true)
                         ->where('stock_quantity', '>', 0);
                  });
            });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVegetarian($query)
    {
        return $query->where('is_vegetarian', true);
    }

    public function scopeVegan($query)
    {
        return $query->where('is_vegan', true);
    }

    public function scopeGlutenFree($query)
    {
        return $query->where('is_gluten_free', true);
    }

    public function scopeSpicy($query)
    {
        return $query->where('is_spicy', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeLowStock($query)
    {
        return $query->where('track_inventory', true)
            ->whereNotNull('low_stock_threshold')
            ->where('stock_quantity', '<=', \DB::raw('low_stock_threshold'));
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('track_inventory', true)
            ->where('stock_quantity', '<=', 0);
    }

    public function scopeTopSelling($query, $limit = 10)
    {
        return $query->orderByDesc('orders_count')->take($limit);
    }

    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhereJsonContains('tags', $term);
        });
    }

    // Méthodes
    public function isAvailable(): bool
    {
        if (!$this->is_available) {
            return false;
        }

        if ($this->track_inventory && $this->stock_quantity <= 0) {
            return false;
        }

        return true;
    }

    public function canBeOrdered(int $quantity = 1): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        if ($this->max_per_order && $quantity > $this->max_per_order) {
            return false;
        }

        if ($this->min_per_order && $quantity < $this->min_per_order) {
            return false;
        }

        if ($this->track_inventory && $quantity > $this->stock_quantity) {
            return false;
        }

        return true;
    }

    public function decrementStock(int $quantity = 1): void
    {
        if ($this->track_inventory) {
            $this->decrement('stock_quantity', $quantity);
            
            // Vérifier le stock bas
            if ($this->low_stock_threshold && $this->stock_quantity <= $this->low_stock_threshold) {
                event(new \App\Events\LowStockAlert($this));
            }
            
            // Vérifier la rupture de stock
            if ($this->stock_quantity <= 0) {
                event(new \App\Events\OutOfStock($this));
            }
        }
    }

    public function incrementStock(int $quantity = 1): void
    {
        if ($this->track_inventory) {
            $this->increment('stock_quantity', $quantity);
        }
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementOrders(int $count = 1): void
    {
        $this->increment('orders_count', $count);
    }

    public function updateRating(): void
    {
        $stats = $this->reviews()
            ->where('is_approved', true)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as count')
            ->first();

        $this->update([
            'rating_avg' => round($stats->avg_rating ?? 0, 2),
            'rating_count' => $stats->count ?? 0,
        ]);
    }

    public function getPriceWithOptions(array $optionIds = []): float
    {
        $totalPrice = $this->price;
        
        if (!empty($optionIds)) {
            $optionsPrice = ProductOptionItem::whereIn('id', $optionIds)->sum('price');
            $totalPrice += $optionsPrice;
        }
        
        return $totalPrice;
    }

    public function duplicate(): self
    {
        $clone = $this->replicate();
        $clone->name = $this->name . ' (Copie)';
        $clone->slug = null;
        $clone->is_available = false;
        $clone->is_featured = false;
        $clone->rating_avg = 0;
        $clone->rating_count = 0;
        $clone->orders_count = 0;
        $clone->views_count = 0;
        $clone->save();
        
        // Dupliquer les images
        foreach ($this->images as $image) {
            $clone->images()->create([
                'path' => $image->path,
                'alt_text' => $image->alt_text,
                'sort_order' => $image->sort_order,
                'is_primary' => $image->is_primary,
            ]);
        }
        
        // Dupliquer les variantes
        foreach ($this->variants as $variant) {
            $clone->variants()->create($variant->toArray());
        }
        
        // Dupliquer les options
        foreach ($this->options as $option) {
            $newOption = $clone->options()->create($option->toArray());
            foreach ($option->items as $item) {
                $newOption->items()->create($item->toArray());
            }
        }
        
        return $clone;
    }

    public function getSimilarProducts(int $limit = 4)
    {
        return self::where('category_id', $this->category_id)
            ->where('id', '!=', $this->id)
            ->available()
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }
}