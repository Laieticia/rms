<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SpecialDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'name',
        'date',
        'open_time',
        'close_time',
        'is_closed',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'open_time' => 'datetime:H:i',
        'close_time' => 'datetime:H:i',
        'is_closed' => 'boolean',
    ];

    protected $appends = [
        'is_today',
        'is_upcoming',
        'is_past',
        'formatted_date',
        'formatted_hours',
        'status_label',
        'status_color',
    ];

    // Types de jours spéciaux
    const TYPES = [
        'holiday' => 'Jour férié',
        'vacation' => 'Congés',
        'event' => 'Événement spécial',
        'maintenance' => 'Maintenance',
        'other' => 'Autre',
    ];

    // Relations
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    // Accesseurs
    public function getIsTodayAttribute(): bool
    {
        return $this->date->isToday();
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->date->isFuture();
    }

    public function getIsPastAttribute(): bool
    {
        return $this->date->isPast();
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('d/m/Y');
    }

    public function getFormattedHoursAttribute(): string
    {
        if ($this->is_closed) {
            return 'Fermé toute la journée';
        }

        if ($this->open_time && $this->close_time) {
            return $this->open_time->format('H:i') . ' - ' . $this->close_time->format('H:i');
        }

        return 'Horaires normaux';
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_past) {
            return 'Passé';
        } elseif ($this->is_today) {
            return 'Aujourd\'hui';
        } else {
            return 'À venir';
        }
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->is_past) {
            return 'secondary';
        } elseif ($this->is_today) {
            return 'warning';
        } else {
            return 'info';
        }
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', today());
    }

    public function scopePast($query)
    {
        return $query->where('date', '<', today());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year);
    }

    public function scopeClosed($query)
    {
        return $query->where('is_closed', true);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_closed', false);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Méthodes
    public function isRestaurantClosed(): bool
    {
        return $this->is_closed;
    }

    public function hasSpecialHours(): bool
    {
        return !$this->is_closed && $this->open_time && $this->close_time;
    }

    public function getOpeningHours(): ?array
    {
        if ($this->is_closed) {
            return null;
        }

        if ($this->open_time && $this->close_time) {
            return [
                'open' => $this->open_time->format('H:i'),
                'close' => $this->close_time->format('H:i'),
            ];
        }

        return null;
    }

    public static function isSpecialDate($restaurantId, $date): bool
    {
        return self::where('restaurant_id', $restaurantId)
            ->whereDate('date', $date)
            ->exists();
    }

    public static function getSpecialDate($restaurantId, $date): ?self
    {
        return self::where('restaurant_id', $restaurantId)
            ->whereDate('date', $date)
            ->first();
    }

    public static function getUpcomingSpecialDays($restaurantId, $limit = 10): array
    {
        return self::where('restaurant_id', $restaurantId)
            ->upcoming()
            ->orderBy('date')
            ->take($limit)
            ->get()
            ->toArray();
    }

    public static function addHoliday($restaurantId, string $name, $date, ?string $description = null): self
    {
        return self::create([
            'restaurant_id' => $restaurantId,
            'name' => $name,
            'date' => $date,
            'is_closed' => true,
            'description' => $description,
        ]);
    }

    public static function addSpecialEvent($restaurantId, string $name, $date, $openTime, $closeTime, ?string $description = null): self
    {
        return self::create([
            'restaurant_id' => $restaurantId,
            'name' => $name,
            'date' => $date,
            'open_time' => $openTime,
            'close_time' => $closeTime,
            'is_closed' => false,
            'description' => $description,
        ]);
    }
}