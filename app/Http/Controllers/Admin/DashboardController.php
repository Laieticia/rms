<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $restaurantId = $this->getRestaurantId($user);

        // Période de filtrage
        $period = $request->get('period', 'today');
        $dateRange = $this->getDateRange($period);

        // Statistiques du jour (pour les cartes du haut)
        $todayStats = [
            'orders' => $this->getOrderCount($restaurantId, $dateRange),
            'revenue' => $this->getTotalRevenue($restaurantId, $dateRange),
            'new_customers' => $this->getNewCustomerCount($restaurantId, $dateRange),
            'average_order' => $this->getAverageOrder($restaurantId, $dateRange),
        ];

        // Statistiques détaillées
        $stats = [
            'total_orders' => $todayStats['orders'],
            'total_revenue' => $todayStats['revenue'],
            'average_order' => $todayStats['average_order'],
            'total_customers' => $this->getCustomerCount($restaurantId, $dateRange),
            'new_customers' => $todayStats['new_customers'],
            'pending_orders' => $this->getOrderCountByStatus($restaurantId, 'pending'),
            'preparing_orders' => $this->getOrderCountByStatus($restaurantId, 'preparing'),
            'ready_orders' => $this->getOrderCountByStatus($restaurantId, 'ready'),
            'in_delivery_orders' => $this->getOrderCountByStatus($restaurantId, 'in_delivery'),
        ];

        // Commandes en attente
        $pendingOrders = Order::with(['user', 'items.product'])
            ->when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();

        // Commandes récentes (tous statuts confondus)
        $recentOrders = Order::with(['user', 'items.product'])
            ->when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->latest()
            ->take(10)
            ->get();

        // Commandes actives
        $activeOrders = Order::with(['user', 'items.product'])
            ->when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->whereIn('status', ['confirmed', 'preparing', 'ready', 'in_delivery'])
            ->latest()
            ->take(10)
            ->get();

        // Produits populaires
        $topProducts = $this->getTopProducts($restaurantId, $dateRange);

        // Graphique des ventes (7 derniers jours)
        $salesChart = $this->getSalesChart($restaurantId);

        // Répartition des commandes par type
        $orderTypes = $this->getOrderTypeDistribution($restaurantId, $dateRange);

        // Avis récents
        $recentReviews = $this->getRecentReviews($restaurantId);

        return view('admin.dashboard', compact(
            'todayStats',
            'stats',
            'period',
            'pendingOrders',
            'recentOrders',
            'activeOrders',
            'topProducts',
            'salesChart',
            'orderTypes',
            'recentReviews'
        ));
    }

    protected function getDateRange(string $period): array
    {
        return match($period) {
            'today' => [Carbon::today(), Carbon::now()],
            'yesterday' => [Carbon::yesterday(), Carbon::yesterday()->endOfDay()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()],
            default => [Carbon::today(), Carbon::now()],
        };
    }

    protected function getOrderCount($restaurantId, array $dateRange): int
    {
        return Order::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->whereBetween('created_at', $dateRange)
            ->count();
    }

    protected function getTotalRevenue($restaurantId, array $dateRange): float
    {
        return Order::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->whereBetween('created_at', $dateRange)
            ->where('payment_status', 'paid')
            ->sum('total');
    }

    protected function getAverageOrder($restaurantId, array $dateRange): float
    {
        return round(Order::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->whereBetween('created_at', $dateRange)
            ->where('payment_status', 'paid')
            ->avg('total') ?? 0, 2);
    }

    protected function getCustomerCount($restaurantId, array $dateRange): int
    {
        return Order::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->whereBetween('created_at', $dateRange)
            ->distinct('user_id')
            ->count('user_id');
    }

    protected function getNewCustomerCount($restaurantId, array $dateRange): int
    {
        return Order::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->whereBetween('created_at', $dateRange)
            ->whereNotIn('user_id', function($query) use ($restaurantId, $dateRange) {
                $query->select('user_id')
                    ->from('orders')
                    ->when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
                    ->where('created_at', '<', $dateRange[0]);
            })
            ->distinct('user_id')
            ->count('user_id');
    }

    protected function getOrderCountByStatus($restaurantId, string $status): int
    {
        return Order::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->where('status', $status)
            ->count();
    }

    protected function getTopProducts($restaurantId, array $dateRange)
    {
        return Product::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->withCount(['orderItems as total_sold' => function($query) use ($dateRange) {
                $query->whereHas('order', function($q) use ($dateRange) {
                    $q->whereBetween('created_at', $dateRange)
                      ->where('payment_status', 'paid');
                });
            }])
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();
    }

    protected function getSalesChart($restaurantId)
    {
        return collect(range(6, 0))->map(function($day) use ($restaurantId) {
            $date = Carbon::now()->subDays($day);
            
            $revenue = Order::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
                ->whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('total');

            $count = Order::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
                ->whereDate('created_at', $date)
                ->count();

            return [
                'date' => $date->format('d/m'),
                'revenue' => $revenue,
                'orders' => $count,
            ];
        });
    }

    protected function getOrderTypeDistribution($restaurantId, array $dateRange)
    {
        // La période du tableau de bord (souvent "aujourd'hui") est trop
        // restrictive pour ce graphique : s'il n'y a pas eu de commande
        // aujourd'hui, le graphique était vide en permanence. On élargit
        // à 30 jours, puis, si toujours vide, sur tout l'historique.
        $distribution = Order::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get();

        if ($distribution->isEmpty()) {
            $distribution = Order::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
                ->select('type', DB::raw('COUNT(*) as count'))
                ->groupBy('type')
                ->get();
        }

        return $distribution;
    }

    protected function getRecentReviews($restaurantId)
    {
        return Review::with(['user', 'order'])
            ->when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->latest()
            ->take(5)
            ->get();
    }

    protected function getRestaurantId($user): ?int
    {
        if ($user->isAdmin()) {
            return request()->get('restaurant_id');
        }

        return $user->restaurants()->first()?->id;
    }
}