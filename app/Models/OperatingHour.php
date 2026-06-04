<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatingHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'day',
        'open_time',
        'close_time',
        'is_closed',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'open_time' => 'datetime:H:i',
        'close_time' => 'datetime:H:i',
    ];

    protected $appends = [
        'day_label',
        'formatted_hours',
        'is_open_now',
    ];

    // Constantes pour les jours
    const DAYS = [
        'monday' => 'Lundi',
        'tuesday' => 'Mardi',
        'wednesday' => 'Mercredi',
        'thursday' => 'Jeudi',
        'friday' => 'Vendredi',
        'saturday' => 'Samedi',
        'sunday' => 'Dimanche',
    ];

    // Relations
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    // Accesseurs
    public function getDayLabelAttribute(): string
    {
        return self::DAYS[$this->day] ?? ucfirst($this->day);
    }

    public function getFormattedHoursAttribute(): string
    {
        if ($this->is_closed) {
            return 'Fermé';
        }

        return $this->open_time->format('H:i') . ' - ' . $this->close_time->format('H:i');
    }

    public function getIsOpenNowAttribute(): bool
    {
        if ($this->is_closed) {
            return false;
        }

        $now = now();
        $currentTime = $now->format('H:i:s');
        $currentDay = strtolower($now->englishDayOfWeek);

        // Vérifier si c'est le bon jour
        if ($this->day !== $currentDay) {
            return false;
        }

        // Gérer les horaires qui traversent minuit
        if ($this->close_time->format('H:i:s') < $this->open_time->format('H:i:s')) {
            // Ex: 22:00 - 02:00
            return $currentTime >= $this->open_time->format('H:i:s') || 
                   $currentTime <= $this->close_time->format('H:i:s');
        }

        // Horaire normal
        return $currentTime >= $this->open_time->format('H:i:s') && 
               $currentTime <= $this->close_time->format('H:i:s');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('is_closed', false);
    }

    public function scopeClosed($query)
    {
        return $query->where('is_closed', true);
    }

    public function scopeByDay($query, string $day)
    {
        return $query->where('day', $day);
    }

    public function scopeToday($query)
    {
        $today = strtolower(now()->englishDayOfWeek);
        return $query->where('day', $today);
    }

    // Méthodes statiques
    public static function getDefaultHours(): array
    {
        $hours = [];
        
        foreach (self::DAYS as $day => $label) {
            $isWeekend = in_array($day, ['saturday', 'sunday']);
            
            $hours[$day] = [
                'open_time' => $isWeekend ? '10:00' : '09:00',
                'close_time' => $isWeekend ? '23:00' : '22:00',
                'is_closed' => false,
            ];
        }

        return $hours;
    }

    // Méthodes d'instance
    public function isOpenAt(string $time): bool
    {
        if ($this->is_closed) {
            return false;
        }

        return $time >= $this->open_time->format('H:i') && 
               $time <= $this->close_time->format('H:i');
    }

    public function getDurationInHours(): float
    {
        if ($this->is_closed) {
            return 0;
        }

        $open = \Carbon\Carbon::parse($this->open_time);
        $close = \Carbon\Carbon::parse($this->close_time);

        if ($close < $open) {
            $close->addDay();
        }

        return $open->diffInHours($close);
    }
}