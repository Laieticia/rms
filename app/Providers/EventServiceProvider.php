<?php

namespace App\Providers;

use App\Events\NewOrderPlaced;
use App\Events\OrderStatusChanged;
use App\Events\PaymentCompleted;
use App\Events\LowStockAlert;
use App\Events\OutOfStock;
use App\Events\DeliveryLocationUpdated;
use App\Events\ReservationCreated;
use App\Listeners\OrderEventListener;
use App\Listeners\InventoryAlertListener;
use App\Listeners\DeliveryTrackingListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NewOrderPlaced::class => [
            OrderEventListener::class . '@handleNewOrder',
        ],
        
        OrderStatusChanged::class => [
            OrderEventListener::class . '@handleStatusChange',
        ],
        
        PaymentCompleted::class => [
            OrderEventListener::class . '@handlePaymentCompleted',
        ],
        
        LowStockAlert::class => [
            InventoryAlertListener::class . '@handleLowStock',
        ],
        
        OutOfStock::class => [
            InventoryAlertListener::class . '@handleOutOfStock',
        ],
        
        DeliveryLocationUpdated::class => [
            DeliveryTrackingListener::class . '@handleLocationUpdate',
        ],
        
        ReservationCreated::class => [
            ReservationListener::class . '@handleNewReservation',
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}