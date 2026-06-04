<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Restaurant;
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

        // Statistiques générales
        $stats = [
            'total_orders' => $this->getOrderCount($restaurantId, $dateRange),
            'total_revenue' => $this->getTotalRevenue($restaurantId, $dateRange),
            'average_order' => $this->getAverageOrder($restaurantId, $dateRange),
            'total_customers' => $this->getCustomerCount($restaurantId, $dateRange),
            'new_customers' => $this->getNewCustomerCount($restaurantId, $dateRange),
            'pending_orders' => $this->getOrderCountByStatus($restaurantId, 'pending'),
            'preparing_orders' => $this->getOrderCountByStatus($restaurantId, 'preparing'),
            'ready_orders' => $this->getOrderCountByStatus($restaurantId, 'ready'),
            'in_delivery_orders' => $this->getOrderCountByStatus($restaurantId, 'in_delivery'),
        ];

        // Commandes récentes
        $recentOrders = $this->getRecentOrders($restaurantId);

        // Produits populaires
        $topProducts = $this->getTopProducts($restaurantId, $dateRange);

        // Graphique des ventes (7 derniers jours)
        $salesChart = $this->getSalesChart($restaurantId);

        // Répartition des commandes par type
        $orderTypes = $this->getOrderTypeDistribution($restaurantId, $dateRange);

        // Avis récents
        $recentReviews = $this->getRecentReviews($restaurantId);

        return view('admin.dashboard', compact(
            'stats',
            'period',
            'recentOrders',
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
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
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
        return Order::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->whereBetween('created_at', $dateRange)
            ->where('payment_status', 'paid')
            ->avg('total') ?? 0;
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

    protected function getRecentOrders($restaurantId)
    {
        return Order::with(['user', 'items.product'])
            ->when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->latest()
            ->take(10)
            ->get();
    }

    protected function getTopProducts($restaurantId, array $dateRange)
    {
        return Product::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->withCount(['orderItems as total_ordered' => function($query) use ($dateRange) {
                $query->whereHas('order', function($q) use ($dateRange) {
                    $q->whereBetween('created_at', $dateRange)
                      ->where('payment_status', 'paid');
                });
            }])
            ->orderByDesc('total_ordered')
            ->take(10)
            ->get();
    }

    protected function getSalesChart($restaurantId)
    {
        $days = collect(range(6, 0))->map(function($day) use ($restaurantId) {
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

        return $days;
    }

    protected function getOrderTypeDistribution($restaurantId, array $dateRange)
    {
        return Order::when($restaurantId, fn($q) => $q->where('restaurant_id', $restaurantId))
            ->whereBetween('created_at', $dateRange)
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get();
    }

    protected function getRecentReviews($restaurantId)
    {
        return \App\Models\Review::with(['user', 'order'])
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