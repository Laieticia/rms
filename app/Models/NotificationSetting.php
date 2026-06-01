<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'email_orders', 'email_promotions', 'sms_orders',
        'push_notifications', 'newsletter'
    ];

    protected $casts = [
        'email_orders' => 'boolean',
        'email_promotions' => 'boolean',
        'sms_orders' => 'boolean',
        'push_notifications' => 'boolean',
        'newsletter' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}