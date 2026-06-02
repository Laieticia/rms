<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\SlugOptions;
use Spatie\Sluggable\HasSlug; // 1. IMPORTATION DU TRAIT AJOUTÉE ICI
use Illuminate\Support\Str;


class Restaurant extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'address',
        'city',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'phone',
        'email',
        'website',
        'logo',
        'cover_image',
        'opening_hours',
        'special_hours',
        'minimum_order',
        'delivery_fee',
        'tax_rate',
        'currency',
        'estimated_delivery_time',
        'is_active',
        'accepts_delivery',
        'accepts_takeaway',
        'accepts_dine_in',
        'delivery_terms',
        'privacy_policy',
        'settings',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'special_hours' => 'array',
        'settings' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'minimum_order' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'estimated_delivery_time' => 'integer',
        'is_active' => 'boolean',
        'accepts_delivery' => 'boolean',
        'accepts_takeaway' => 'boolean',
        'accepts_dine_in' => 'boolean',
    ];

    protected $appends = ['logo_url', 'cover_url', 'average_rating', 'total_reviews'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    // Relations
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'permissions')
            ->withTimestamps();
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->wherePivot('role', 'manager');
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->wherePivotIn('role', ['chef', 'waiter']);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class)->orderBy('sort_order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    public function loyaltyRewards(): HasMany
    {
        return $this->hasMany(LoyaltyReward::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function deliveryZones(): HasMany
    {
        return $this->hasMany(DeliveryZone::class);
    }

    public function operatingHours(): HasMany
    {
        return $this->hasMany(OperatingHour::class);
    }

    public function specialDays(): HasMany
    {
        return $this->hasMany(SpecialDay::class);
    }

    // Accesseurs
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getTotalReviewsAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function getFormattedAddressAttribute(): string
    {
        return "{$this->address}, {$this->postal_code} {$this->city}";
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    public function scopeNearby($query, $latitude, $longitude, $radius = 10)
    {
        return $query->selectRaw("
            *, 
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) 
            * cos(radians(longitude) - radians(?)) + sin(radians(?)) 
            * sin(radians(latitude)))) AS distance
        ", [$latitude, $longitude, $latitude])
        ->having('distance', '<=', $radius)
        ->orderBy('distance');
    }

    public function scopeOpenNow($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $dayOfWeek = strtolower(now()->format('l'));
                $currentTime = now()->format('H:i:s');
                
                $q->whereHas('operatingHours', function($qh) use ($dayOfWeek, $currentTime) {
                    $qh->where('day', $dayOfWeek)
                      ->where('is_closed', false)
                      ->where('open_time', '<=', $currentTime)
                      ->where('close_time', '>=', $currentTime);
                });
            });
    }

    // Méthodes
    public function isOpen(): bool
    {
        // Vérifier les jours spéciaux d'abord
        $specialDay = $this->specialDays()
            ->whereDate('date', today())
            ->first();

        if ($specialDay) {
            return !$specialDay->is_closed;
        }

        $dayOfWeek = strtolower(now()->format('l'));
        $currentTime = now()->format('H:i:s');

        return $this->operatingHours()
            ->where('day', $dayOfWeek)
            ->where('is_closed', false)
            ->where('open_time', '<=', $currentTime)
            ->where('close_time', '>=', $currentTime)
            ->exists();
    }

    public function canDeliverTo(float $latitude, float $longitude): bool
    {
        if (!$this->accepts_delivery) {
            return false;
        }

        return $this->deliveryZones()
            ->where('is_active', true)
            ->whereRaw("ST_Contains(ST_GeomFromText(CONCAT('POLYGON((', coordinates, '))')), POINT(?, ?))", 
                [$longitude, $latitude])
            ->exists();
    }

    public function getDeliveryFee(float $latitude, float $longitude): float
    {
        $zone = $this->deliveryZones()
            ->where('is_active', true)
            ->whereRaw("ST_Contains(ST_GeomFromText(CONCAT('POLYGON((', coordinates, '))')), POINT(?, ?))", 
                [$longitude, $latitude])
            ->first();

        return $zone ? $zone->delivery_fee : $this->delivery_fee;
    }

    public function getEstimatedDeliveryTime(float $latitude, float $longitude): int
    {
        $zone = $this->deliveryZones()
            ->where('is_active', true)
            ->whereRaw("ST_Contains(ST_GeomFromText(CONCAT('POLYGON((', coordinates, '))')), POINT(?, ?))", 
                [$longitude, $latitude])
            ->first();

        return $zone ? $zone->estimated_time : $this->estimated_delivery_time;
    }

    public function getTodayStats(): array
    {
        $today = today();
        
        $orders = $this->orders()->whereDate('created_at', $today);
        
        return [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->where('payment_status', 'paid')->sum('total'),
            'new_orders' => $orders->where('status', 'pending')->count(),
            'in_progress' => $orders->whereIn('status', ['confirmed', 'preparing'])->count(),
            'ready' => $orders->where('status', 'ready')->count(),
            'in_delivery' => $orders->where('status', 'in_delivery')->count(),
            'completed' => $orders->whereIn('status', ['delivered', 'completed'])->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
            'average_order' => $orders->where('payment_status', 'paid')->avg('total') ?? 0,
        ];
    }

    public function getMonthlyRevenue(): float
    {
        return $this->orders()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('payment_status', 'paid')
            ->sum('total');
    }

    public function getTopProducts(int $limit = 10): array
    {
        return $this->products()
            ->withCount(['orderItems as total_ordered' => function($query) {
                $query->whereHas('order', function($q) {
                    $q->where('payment_status', 'paid');
                });
            }])
            ->orderByDesc('total_ordered')
            ->take($limit)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'total_ordered' => $product->total_ordered,
                    'revenue' => $product->total_ordered * $product->price,
                ];
            })
            ->toArray();
    }

    public function getCustomerStats(): array
    {
        return [
            'total_customers' => $this->orders()->distinct('user_id')->count('user_id'),
            'new_customers_today' => $this->orders()
                ->whereDate('created_at', today())
                ->whereNotIn('user_id', function($query) {
                    $query->select('user_id')
                        ->from('orders')
                        ->where('restaurant_id', $this->id)
                        ->whereDate('created_at', '<', today());
                })
                ->distinct('user_id')
                ->count('user_id'),
            'returning_customers' => $this->orders()
                ->select('user_id')
                ->selectRaw('COUNT(*) as order_count')
                ->groupBy('user_id')
                ->having('order_count', '>', 1)
                ->count(),
        ];
    }
}