<?php
// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\HasOrderNumber;

class Order extends Model
{
    use SoftDeletes, HasOrderNumber;

    protected $fillable = [
        'restaurant_id', 'user_id', 'address_id', 'coupon_id',
        'delivery_person_id', 'order_number', 'type', 'status',
        'table_number', 'delivery_address', 'delivery_city',
        'delivery_postal_code', 'delivery_latitude', 'delivery_longitude',
        'delivery_instructions', 'estimated_delivery_time',
        'delivered_at', 'subtotal', 'tax_amount', 'delivery_fee',
        'discount_amount', 'tip_amount', 'total', 'payment_method',
        'payment_status', 'payment_id', 'payment_gateway',
        'payment_details', 'paid_at', 'notes', 'kitchen_notes',
        'cancellation_reason', 'confirmed_at', 'prepared_at',
        'ready_at', 'source', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'payment_details' => 'array',
        'confirmed_at' => 'datetime',
        'prepared_at' => 'datetime',
        'ready_at' => 'datetime',
        'delivered_at' => 'datetime',
        'paid_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tip_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected $appends = ['status_label', 'status_color', 'formatted_total'];

    // Statuts disponibles
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PREPARING = 'preparing';
    const STATUS_READY = 'ready';
    const STATUS_IN_DELIVERY = 'in_delivery';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Relations
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function deliveryPerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_person_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function deliveryTracking(): HasMany
    {
        return $this->hasMany(DeliveryTracking::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
// Accesseurs
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'En attente',
            self::STATUS_CONFIRMED => 'Confirmée',
            self::STATUS_PREPARING => 'En préparation',
            self::STATUS_READY => 'Prête',
            self::STATUS_IN_DELIVERY => 'En livraison',
            self::STATUS_DELIVERED => 'Livrée',
            self::STATUS_COMPLETED => 'Terminée',
            self::STATUS_CANCELLED => 'Annulée',
            default => 'Inconnu'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_CONFIRMED => 'info',
            self::STATUS_PREPARING => 'primary',
            self::STATUS_READY => 'success',
            self::STATUS_IN_DELIVERY => 'primary',
            self::STATUS_DELIVERED => 'success',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'danger',
            default => 'secondary'
        };
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 2, ',', ' ') . ' €';
    }

    // Méthodes
    public function updateStatus(string $status, ?string $comment = null): void
    {
        $oldStatus = $this->status;
        $this->update(['status' => $status]);

        $this->statusHistory()->create([
            'user_id' => auth()->id(),
            'status' => $status,
            'comment' => $comment,
        ]);

        // Événement de changement de statut
        event(new OrderStatusChanged($this, $oldStatus, $status));

        // Notifications selon le statut
        $this->sendStatusNotification($status);
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('total_price');
        $this->tax_amount = $this->subtotal * ($this->restaurant->tax_rate / 100);
        
        if ($this->coupon) {
            $this->discount_amount = $this->calculateDiscount();
        }

        $this->total = $this->subtotal + $this->tax_amount + $this->delivery_fee 
                      - $this->discount_amount + $this->tip_amount;
        
        if ($this->total < 0) {
            $this->total = 0;
        }

        $this->save();
    }

    public function calculateDiscount(): float
    {
        if (!$this->coupon) {
            return 0;
        }

        return $this->coupon->calculateDiscount($this->subtotal);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
        ]);
    }

    public function cancel(string $reason): void
    {
        if (!$this->canBeCancelled()) {
            throw new \Exception('Cette commande ne peut plus être annulée.');
        }

        $this->updateStatus(self::STATUS_CANCELLED, $reason);
        
        // Rembourser si payé
        if ($this->payment_status === 'paid') {
            $this->processRefund();
        }
    }

    protected function sendStatusNotification(string $status): void
    {
        $notificationData = [
            'order_id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $status,
        ];

        match($status) {
            self::STATUS_CONFIRMED => $this->user->notify(new OrderConfirmed($this)),
            self::STATUS_PREPARING => $this->user->notify(new OrderPreparing($this)),
            self::STATUS_READY => $this->user->notify(new OrderReady($this)),
            self::STATUS_IN_DELIVERY => $this->user->notify(new OrderInDelivery($this)),
            self::STATUS_DELIVERED => $this->user->notify(new OrderDelivered($this)),
            default => null
        };
    }
}