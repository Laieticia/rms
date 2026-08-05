<?php

namespace App\Listeners;

use App\Events\LowStockAlert;
use App\Events\OutOfStock;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class InventoryAlertListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Alerte de stock faible : on prévient le staff du restaurant concerné.
     */
    public function handleLowStock(LowStockAlert $event): void
    {
        $this->notificationService->notifyRestaurantStaff(
            $event->restaurant,
            'Stock faible',
            $event->getMessage(),
            [
                'type' => 'low_stock',
                'product_id' => $event->product->id,
                'alert_level' => $event->getAlertLevel(),
            ]
        );
    }

    /**
     * Rupture de stock : alerte prioritaire au staff, le produit devient indisponible.
     */
    public function handleOutOfStock(OutOfStock $event): void
    {
        $this->notificationService->notifyRestaurantStaff(
            $event->restaurant,
            'Rupture de stock',
            $event->getMessage(),
            [
                'type' => 'out_of_stock',
                'product_id' => $event->product->id,
                'priority' => 'high',
            ]
        );
    }
}
