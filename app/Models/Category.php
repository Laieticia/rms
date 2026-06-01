<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'restaurant_id',
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'sort_order',
        'is_active',
        'available_from',
        'available_until',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
    ];

    protected $appends = ['image_url', 'products_count'];

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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class)->orderBy('sort_order');
    }

    public function availableProducts(): HasMany
    {
        return $this->products()->available();
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_category');
    }

    // Accesseurs
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getProductsCountAttribute(): int
    {
        return $this->products()->available()->count();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $now = now();
                $q->whereNull('available_from')
                  ->orWhere('available_from', '<=', $now);
            })
            ->where(function($q) {
                $now = now();
                $q->whereNull('available_until')
                  ->orWhere('available_until', '>=', $now);
            });
    }

    // Méthodes
    public function isAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->available_from && $this->available_from > $now) {
            return false;
        }
        
        if ($this->available_until && $this->available_until < $now) {
            return false;
        }

        return true;
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    public function hasProducts(): bool
    {
        return $this->products()->exists();
    }

    public function getAllProducts()
    {
        $productIds = $this->products()->pluck('id');
        
        foreach ($this->children as $child) {
            $productIds = $productIds->merge($child->getAllProductsIds());
        }
        
        return Product::whereIn('id', $productIds)->get();
    }

    protected function getAllProductsIds()
    {
        $productIds = $this->products()->pluck('id');
        
        foreach ($this->children as $child) {
            $productIds = $productIds->merge($child->getAllProductsIds());
        }
        
        return $productIds;
    }
}