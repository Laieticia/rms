<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id', 'user_id', 'reservation_number', 'date',
        'time', 'guests_count', 'special_requests', 'status',
        'table_number', 'customer_name', 'customer_phone',
        'customer_email', 'cancellation_reason'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}