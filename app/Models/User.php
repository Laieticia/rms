<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'avatar',
        'email_verified_at',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'last_login_at',
        'last_login_ip',
        'is_active',
        'is_blocked',
        'blocked_until',
        'blocked_reason',
        'preferences',
        'metadata',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'blocked_until' => 'datetime',
        'is_active' => 'boolean',
        'is_blocked' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'preferences' => 'array',
        'metadata' => 'array',
    ];

    protected $appends = ['full_name', 'avatar_url'];

    // Relations
    public function restaurants(): BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class)
            ->withPivot('role', 'permissions')
            ->withTimestamps();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function loyaltyPoints(): HasMany
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    public function loyaltyRedemptions(): HasMany
    {
        return $this->hasMany(LoyaltyRedemption::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function notificationSettings(): HasOne
    {
        return $this->hasOne(NotificationSetting::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Order::class, 'delivery_person_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function referralCode(): HasOne
    {
        return $this->hasOne(ReferralCode::class);
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    // Accesseurs & Mutateurs
    public function getFullNameAttribute(): string
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&background=random&size=200';
    }

    public function setPasswordAttribute($value): void
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('is_blocked', false)
                    ->where(function($q) {
                        $q->whereNull('blocked_until')
                          ->orWhere('blocked_until', '<', now());
                    });
    }

    public function scopeCustomers($query)
    {
        return $query->role('customer');
    }

    public function scopeDeliveryPersons($query)
    {
        return $query->role('delivery_person');
    }

    public function scopeStaff($query)
    {
        return $query->role(['admin', 'manager', 'chef', 'waiter']);
    }

    // Méthodes
    public function isAdmin(): bool
    {
        return $this->hasRole(['super_admin', 'admin']);
    }

    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    public function isStaff(): bool
    {
        return $this->hasRole(['admin', 'manager', 'chef', 'waiter']);
    }

    public function canManageRestaurant(Restaurant $restaurant): bool
    {
        return $this->restaurants()
            ->where('restaurant_id', $restaurant->id)
            ->exists();
    }

    public function getLoyaltyBalance(): int
    {
        return $this->loyaltyPoints()
            ->where('type', 'earned')
            ->sum('points') - 
            $this->loyaltyPoints()
            ->where('type', 'spent')
            ->sum('points');
    }

    public function addLoyaltyPoints(int $points, string $description, ?Order $order = null): void
    {
        $currentBalance = $this->getLoyaltyBalance();
        
        $this->loyaltyPoints()->create([
            'order_id' => $order?->id,
            'points' => $points,
            'type' => 'earned',
            'description' => $description,
            'balance_before' => $currentBalance,
            'balance_after' => $currentBalance + $points,
        ]);
    }

    public function spendLoyaltyPoints(int $points, string $description, ?Order $order = null): void
    {
        $currentBalance = $this->getLoyaltyBalance();
        
        if ($currentBalance < $points) {
            throw new \Exception('Points de fidélité insuffisants');
        }
        
        $this->loyaltyPoints()->create([
            'order_id' => $order?->id,
            'points' => $points,
            'type' => 'spent',
            'description' => $description,
            'balance_before' => $currentBalance,
            'balance_after' => $currentBalance - $points,
        ]);
    }

    public function hasDefaultAddress(): bool
    {
        return $this->addresses()->where('is_default', true)->exists();
    }

    public function getDefaultAddress(): ?Address
    {
        return $this->addresses()->where('is_default', true)->first();
    }

    public function recordLogin(): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
        
        // Enregistrer dans l'historique
        LoginHistory::create([
            'user_id' => $this->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'login_at' => now(),
        ]);
    }

    public function getOrderStats(): array
    {
        return [
            'total_orders' => $this->orders()->count(),
            'completed_orders' => $this->orders()->whereIn('status', ['completed', 'delivered'])->count(),
            'cancelled_orders' => $this->orders()->where('status', 'cancelled')->count(),
            'total_spent' => $this->orders()->where('payment_status', 'paid')->sum('total'),
            'average_order' => $this->orders()->where('payment_status', 'paid')->avg('total') ?? 0,
            'first_order_at' => $this->orders()->min('created_at'),
            'last_order_at' => $this->orders()->max('created_at'),
            'favorite_restaurant' => $this->getFavoriteRestaurant(),
        ];
    }

    public function getFavoriteRestaurant(): ?Restaurant
    {
        return Restaurant::select('restaurants.*')
            ->selectRaw('COUNT(orders.id) as orders_count')
            ->join('orders', 'restaurants.id', '=', 'orders.restaurant_id')
            ->where('orders.user_id', $this->id)
            ->whereIn('orders.status', ['completed', 'delivered'])
            ->groupBy('restaurants.id')
            ->orderByDesc('orders_count')
            ->first();
    }
}