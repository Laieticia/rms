<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\HasRestaurant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    use HasRestaurant;

    public function index(Request $request)
    {
        $restaurantId = $this->getRestaurantId();
        
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        // Statistiques des ventes
        $salesStats = [
            'total_orders' => Order::where('restaurant_id', $restaurantId)
                ->whereBetween('created_at', $dateRange)
                ->count(),
            'total_revenue' => Order::where('restaurant_id', $restaurantId)
                ->whereBetween('created_at', $dateRange)
                ->paid()
                ->sum('total'),
            'total_discounts' => Order::where('restaurant_id', $restaurantId)
                ->whereBetween('created_at', $dateRange)
                ->sum('discount_amount'),
            'average_order' => Order::where('restaurant_id', $restaurantId)
                ->whereBetween('created_at', $dateRange)
                ->paid()
                ->avg('total') ?? 0,
            'completion_rate' => $this->getCompletionRate($restaurantId, $dateRange),
            'cancellation_rate' => $this->getCancellationRate($restaurantId, $dateRange),
        ];

        // Commandes par type
        $ordersByType = Order::where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', $dateRange)
            ->select('type', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as revenue'))
            ->groupBy('type')
            ->get();

        // Commandes par statut
        $ordersByStatus = Order::where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', $dateRange)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Top produits
        $topProducts = Product::where('restaurant_id', $restaurantId)
            ->withCount(['orderItems as total_sold' => function($q) use ($dateRange) {
                $q->whereHas('order', function($oq) use ($dateRange) {
                    $oq->whereBetween('created_at', $dateRange);
                });
            }])
            ->orderByDesc('total_sold')
            ->take(10)
            ->get();

        // Top clients
        $topCustomers = User::role('customer')
            ->withCount(['orders as total_orders' => function($q) use ($restaurantId, $dateRange) {
                $q->where('restaurant_id', $restaurantId)
                  ->whereBetween('created_at', $dateRange);
            }])
            ->withSum(['orders as total_spent' => function($q) use ($restaurantId, $dateRange) {
                $q->where('restaurant_id', $restaurantId)
                  ->whereBetween('created_at', $dateRange)
                  ->where('payment_status', 'paid');
            }], 'total')
            ->orderByDesc('total_spent')
            ->take(10)
            ->get();

        // Ventes par jour (pour graphique)
        $dailySales = [];
        $current = $dateRange[0]->copy();
        while ($current <= $dateRange[1]) {
            $dailySales[] = [
                'date' => $current->format('Y-m-d'),
                'revenue' => Order::where('restaurant_id', $restaurantId)
                    ->whereDate('created_at', $current)
                    ->paid()
                    ->sum('total'),
                'orders' => Order::where('restaurant_id', $restaurantId)
                    ->whereDate('created_at', $current)
                    ->count(),
            ];
            $current->addDay();
        }

        $dailySales = collect($dailySales);

        return view('admin.reports.index', compact(
            'period', 'salesStats', 'ordersByType', 'ordersByStatus',
            'topProducts', 'topCustomers', 'dailySales'
        ));
    }

    public function export(Request $request)
    {
        $restaurantId = $this->getRestaurantId();
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        $orders = Order::with(['user', 'items.product'])
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', $dateRange)->get();

        $filename = 'rapport_' . now()->format('Ymd_His') . '.csv';

        return response()->stream(function() use ($orders) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['N° Commande', 'Date', 'Client', 'Type', 'Articles', 'Total', 'Statut'], ';');
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->user->full_name,
                    $order->type_label ?? $order->type,
                    $order->items->sum('quantity'),
                    number_format($order->total, 2, ',', ''),
                    $order->status_label ?? $order->status,
                ], ';');
            }
            fclose($file);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function getCompletionRate($restaurantId, $dateRange): float
    {
        $total = Order::where('restaurant_id', $restaurantId)->whereBetween('created_at', $dateRange)->count();
        if ($total === 0) return 0;
        $completed = Order::where('restaurant_id', $restaurantId)->whereBetween('created_at', $dateRange)->whereIn('status', ['delivered', 'completed'])->count();
        return round(($completed / $total) * 100, 1);
    }

    private function getCancellationRate($restaurantId, $dateRange): float
    {
        $total = Order::where('restaurant_id', $restaurantId)->whereBetween('created_at', $dateRange)->count();
        if ($total === 0) return 0;
        $cancelled = Order::where('restaurant_id', $restaurantId)->whereBetween('created_at', $dateRange)->where('status', 'cancelled')->count();
        return round(($cancelled / $total) * 100, 1);
    }

    private function getDateRange(string $period): array
    {
        return match($period) {
            'today' => [Carbon::today(), Carbon::now()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()],
        };
    }
    
}