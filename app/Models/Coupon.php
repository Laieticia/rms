<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'code',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'max_uses',
        'used_count',
        'max_uses_per_user',
        'is_active',
        'starts_at',
        'expires_at',
        'applies_to_all',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'max_uses_per_user' => 'integer',
        'is_active' => 'boolean',
        'applies_to_all' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $appends = [
        'is_valid',
        'is_expired',
        'usage_percentage',
        'formatted_value',
    ];

    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_FIXED_AMOUNT = 'fixed_amount';
    const TYPE_FREE_DELIVERY = 'free_delivery';

    // Relations
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'coupon_product');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'coupon_category');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Accesseurs
    public function getIsValidAttribute(): bool
    {
        return $this->is_active
            && !$this->is_expired
            && ($this->max_uses === null || $this->used_count < $this->max_uses);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getUsagePercentageAttribute(): float
    {
        if ($this->max_uses) {
            return ($this->used_count / $this->max_uses) * 100;
        }
        return 0;
    }

    public function getFormattedValueAttribute(): string
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            return $this->value . '%';
        }
        return number_format($this->value, 2, ',', ' ') . ' €';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            })
            ->where(function($q) {
                $q->whereNull('max_uses')
                  ->orWhereRaw('used_count < max_uses');
            });
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    // Méthodes
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function isValidForUser(User $user): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->max_uses_per_user) {
            $userUses = $this->orders()
                ->where('user_id', $user->id)
                ->count();
            
            if ($userUses >= $this->max_uses_per_user) {
                return false;
            }
        }

        return true;
    }

    public function calculateDiscount(float $orderAmount): float
    {
        if (!$this->isValid() || $orderAmount < $this->min_order_amount) {
            return 0;
        }

        $discount = match($this->type) {
            self::TYPE_PERCENTAGE => $orderAmount * ($this->value / 100),
            self::TYPE_FIXED_AMOUNT => $this->value,
            self::TYPE_FREE_DELIVERY => 0, // Géré séparément
            default => 0,
        };

        // Appliquer le plafond si défini
        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        // Le discount ne peut pas dépasser le montant de la commande
        if ($discount > $orderAmount) {
            $discount = $orderAmount;
        }

        return round($discount, 2);
    }

    public function appliesToProduct(Product $product): bool
    {
        if ($this->applies_to_all) {
            return true;
        }

        // Vérifier si le produit est directement lié
        if ($this->products->contains($product->id)) {
            return true;
        }

        // Vérifier si la catégorie du produit est liée
        return $this->categories->contains($product->category_id);
    }

    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    public function decrementUsage(): void
    {
        if ($this->used_count > 0) {
            $this->decrement('used_count');
        }
    }

    public function getUsageStats(): array
    {
        return [
            'total_uses' => $this->used_count,
            'max_uses' => $this->max_uses,
            'remaining' => $this->max_uses ? $this->max_uses - $this->used_count : 'Illimité',
            'total_saved' => $this->orders()->sum('discount_amount'),
            'average_order' => $this->orders()->avg('total'),
        ];
    }

    public static function generateUniqueCode(int $length = 8): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        do {
            $code = substr(str_shuffle(str_repeat($characters, $length)), 0, $length);
        } while (self::where('code', $code)->exists());

        return $code;
    }
}