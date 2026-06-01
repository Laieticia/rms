<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'reward_id', 'order_id', 'points_spent',
        'code', 'status', 'expires_at', 'redeemed_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'redeemed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reward()
    {
        return $this->belongsTo(LoyaltyReward::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}