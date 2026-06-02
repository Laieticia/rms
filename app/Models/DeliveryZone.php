<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'name',
        'description',
        'coordinates',
        'delivery_fee',
        'min_order_amount',
        'estimated_time',
        'is_active',
    ];

    protected $casts = [
        'coordinates' => 'array',
        'delivery_fee' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'estimated_time' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relations
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Méthodes
    public function containsPoint(float $latitude, float $longitude): bool
    {
        // Algorithme de point dans un polygone
        $vertices = $this->coordinates;
        $vertexCount = count($vertices);
        $inside = false;

        for ($i = 0, $j = $vertexCount - 1; $i < $vertexCount; $j = $i++) {
            if (($vertices[$i]['lat'] > $latitude) != ($vertices[$j]['lat'] > $latitude) &&
                ($longitude < ($vertices[$j]['lng'] - $vertices[$i]['lng']) * 
                ($latitude - $vertices[$i]['lat']) / 
                ($vertices[$j]['lat'] - $vertices[$i]['lat']) + $vertices[$i]['lng'])) {
                $inside = !$inside;
            }
        }

        return $inside;
    }
}