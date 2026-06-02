<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryTracking extends Model
{
    use HasFactory;

     protected $fillable = [
        'order_id',
        'latitude',
        'longitude',
        'speed',
        'status',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'speed' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    // Relations
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopeForOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    public function scopeRecent($query)
    {
        return $query->where('recorded_at', '>=', now()->subMinutes(5));
    }

    // Méthodes
    public function getFormattedSpeedAttribute(): string
    {
        return $this->speed ? round($this->speed, 1) . ' km/h' : 'N/A';
    }

    public static function getLatestPosition($orderId)
    {
        return self::where('order_id', $orderId)
            ->orderBy('recorded_at', 'desc')
            ->first();
    }

    public static function getRoute($orderId, $limit = 50)
    {
        return self::where('order_id', $orderId)
            ->orderBy('recorded_at', 'asc')
            ->take($limit)
            ->get(['latitude', 'longitude', 'recorded_at']);
    }
}