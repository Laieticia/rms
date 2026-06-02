<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'address_id',
        'coupon_id',
        'delivery_person_id',
        'order_number',
        'type',
        'status',
        'table_number',
        'delivery_address',
        'delivery_city',
        'delivery_postal_code',
        'delivery_latitude',
        'delivery_longitude',
        'delivery_instructions',
        'estimated_delivery_time',
        'delivered_at',
        'subtotal',
        'tax_amount',
        'delivery_fee',
        'discount_amount',
        'tip_amount',
        'total',
        'payment_method',
        'payment_status',
        'payment_id',
        'payment_gateway',
        'payment_details',
        'paid_at',
        'notes',
        'kitchen_notes',
        'cancellation_reason',
        'confirmed_at',
        'prepared_at',
        'ready_at',
        'source',
        'ip_address',
        'user_agent',
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
        'delivery_latitude' => 'decimal:8',
        'delivery_longitude' => 'decimal:8',
        'estimated_delivery_time' => 'integer',
    ];

    protected $appends = [
        'status_label',
        'status_color',
        'status_icon',
        'formatted_total',
        'formatted_subtotal',
        'can_be_cancelled',
        'preparation_time',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PREPARING = 'preparing';
    const STATUS_READY = 'ready';
    const STATUS_IN_DELIVERY = 'in_delivery';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const TYPE_DINE_IN = 'dine_in';
    const TYPE_TAKEAWAY = 'takeaway';
    const TYPE_DELIVERY = 'delivery';

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
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function deliveryTracking(): HasMany
    {
        return $this->hasMany(DeliveryTracking::class)->orderBy('recorded_at', 'desc');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function loyaltyPoints(): HasMany
    {
        return $this->hasMany(LoyaltyPoint::class);
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
            default => 'Inconnu',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_CONFIRMED => 'info',
            self::STATUS_PREPARING => 'primary',
            self::STATUS_READY => 'success',
            self::STATUS_IN_DELIVERY => 'info',
            self::STATUS_DELIVERED => 'success',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'clock',
            self::STATUS_CONFIRMED => 'check-circle',
            self::STATUS_PREPARING => 'fire',
            self::STATUS_READY => 'check-all',
            self::STATUS_IN_DELIVERY => 'truck',
            self::STATUS_DELIVERED => 'box-seam',
            self::STATUS_COMPLETED => 'star',
            self::STATUS_CANCELLED => 'x-circle',
            default => 'question-circle',
        };
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 2, ',', ' ') . ' €';
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal, 2, ',', ' ') . ' €';
    }

    public function getCanBeCancelledAttribute(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
        ]);
    }

    public function getPreparationTimeAttribute(): ?string
    {
        if ($this->confirmed_at && $this->ready_at) {
            return $this->confirmed_at->diffInMinutes($this->ready_at) . ' min';
        }
        return null;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
            self::STATUS_DELIVERED,
        ]);
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

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForDelivery($query)
    {
        return $query->where('type', self::TYPE_DELIVERY);
    }

    // Méthodes
    public static function generateOrderNumber(): string
    {
        $prefix = date('Ymd');
        $lastOrder = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "ORD-{$prefix}-{$newNumber}";
    }

    public function calculateTotals(): self
    {
        // Calcul du sous-total
        $this->subtotal = $this->items->sum('total_price');
        
        // Calcul de la TVA
        $this->tax_amount = $this->subtotal * ($this->restaurant->tax_rate / 100);
        
        // Application du coupon
        $this->discount_amount = 0;
        if ($this->coupon) {
            $this->discount_amount = $this->coupon->calculateDiscount($this->subtotal);
        }
        
        // Frais de livraison
        if ($this->type === self::TYPE_DELIVERY && $this->delivery_fee === null) {
            $this->delivery_fee = $this->restaurant->getDeliveryFee(
                $this->delivery_latitude,
                $this->delivery_longitude
            );
        }
        
        // Total
        $this->total = $this->subtotal 
                      + $this->tax_amount 
                      + $this->delivery_fee 
                      - $this->discount_amount 
                      + $this->tip_amount;
        
        if ($this->total < 0) {
            $this->total = 0;
        }
        
        return $this;
    }

    public function updateStatus(string $status, ?string $comment = null, ?User $user = null): void
    {
        $oldStatus = $this->status;
        
        $updateData = ['status' => $status];
        
        // Mettre à jour les timestamps selon le statut
        switch ($status) {
            case self::STATUS_CONFIRMED:
                $updateData['confirmed_at'] = now();
                break;
            case self::STATUS_PREPARING:
                $updateData['prepared_at'] = now();
                break;
            case self::STATUS_READY:
                $updateData['ready_at'] = now();
                break;
            case self::STATUS_DELIVERED:
            case self::STATUS_COMPLETED:
                $updateData['delivered_at'] = now();
                break;
        }
        
        $this->update($updateData);
        
        // Historique
        $this->statusHistory()->create([
            'user_id' => $user ? $user->id : auth()->id(),
            'status' => $status,
            'comment' => $comment,
        ]);
        
        // Événement
        event(new \App\Events\OrderStatusChanged($this, $oldStatus, $status));
        
        // Notifications
        $this->sendStatusNotification($status);
        
        // Points de fidélité
        if ($status === self::STATUS_COMPLETED) {
            $this->awardLoyaltyPoints();
        }
    }

    public function assignDeliveryPerson(User $deliveryPerson): void
    {
        if (!$deliveryPerson->hasRole('delivery_person')) {
            throw new \Exception('L\'utilisateur n\'est pas un livreur');
        }
        
        $this->update([
            'delivery_person_id' => $deliveryPerson->id,
        ]);
        
        if ($this->status === self::STATUS_READY) {
            $this->updateStatus(self::STATUS_IN_DELIVERY, 'Livreur assigné');
        }
        
        // Notification au livreur
        $deliveryPerson->notify(new \App\Notifications\DeliveryAssigned($this));
    }

    public function canBeReviewedBy(User $user): bool
    {
        return $this->user_id === $user->id 
            && in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_DELIVERED])
            && !$this->review()->exists();
    }

    public function awardLoyaltyPoints(): void
    {
        $pointsEarned = floor($this->total);
        
        $this->user->addLoyaltyPoints(
            $pointsEarned,
            "Commande #{$this->order_number}",
            $this
        );
    }

    public function processRefund(string $reason = ''): void
    {
        if ($this->payment_status !== 'paid') {
            throw new \Exception('La commande n\'a pas été payée');
        }
        
        // Logique de remboursement selon la passerelle de paiement
        if ($this->payment_gateway === 'stripe') {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            
            try {
                $refund = \Stripe\Refund::create([
                    'payment_intent' => $this->payment_id,
                    'reason' => 'requested_by_customer',
                ]);
                
                $this->update([
                    'payment_status' => 'refunded',
                ]);
                
                $this->payment()->create([
                    'type' => 'refund',
                    'amount' => $this->total,
                    'status' => 'completed',
                    'gateway' => 'stripe',
                    'transaction_id' => $refund->id,
                    'metadata' => [
                        'reason' => $reason,
                        'original_payment_id' => $this->payment_id,
                    ],
                ]);
            } catch (\Exception $e) {
                throw new \Exception('Échec du remboursement : ' . $e->getMessage());
            }
        }
    }

    protected function sendStatusNotification(string $status): void
    {
        $notificationMap = [
            self::STATUS_CONFIRMED => \App\Notifications\OrderConfirmed::class,
            self::STATUS_PREPARING => \App\Notifications\OrderPreparing::class,
            self::STATUS_READY => \App\Notifications\OrderReady::class,
            self::STATUS_IN_DELIVERY => \App\Notifications\OrderInDelivery::class,
            self::STATUS_DELIVERED => \App\Notifications\OrderDelivered::class,
            self::STATUS_CANCELLED => \App\Notifications\OrderCancelled::class,
        ];
        
        if (isset($notificationMap[$status])) {
            $this->user->notify(new $notificationMap[$status]($this));
            
            // SMS pour les statuts importants
            if (in_array($status, [self::STATUS_READY, self::STATUS_IN_DELIVERY]) 
                && $this->user->phone) {
                $this->user->notify(new \App\Notifications\OrderStatusSMS($this));
            }
        }
    }
}