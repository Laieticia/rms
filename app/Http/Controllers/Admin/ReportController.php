<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
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
        $startDate = $dateRange[0]->copy();
        $endDate = $dateRange[1]->copy();
        
        while ($startDate <= $endDate) {
            $dailySales[] = [
                'date' => $startDate->format('Y-m-d'),
                'revenue' => Order::where('restaurant_id', $restaurantId)
                    ->whereDate('created_at', $startDate)
                    ->paid()
                    ->sum('total'),
                'orders' => Order::where('restaurant_id', $restaurantId)
                    ->whereDate('created_at', $startDate)
                    ->count(),
            ];
            $startDate->addDay();
        }

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
            ->whereBetween('created_at', $dateRange)
            ->get();

        $filename = 'rapport_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

            fputcsv($file, [
                'N° Commande', 'Date', 'Client', 'Email', 'Téléphone',
                'Articles', 'Sous-total', 'Remise', 'Livraison', 'TVA', 'Total',
                'Paiement', 'Statut',
            ], ';');

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->user->full_name,
                    $order->user->email,
                    $order->user->phone,
                    $order->items->sum('quantity'),
                    number_format($order->subtotal, 2, ',', ''),
                    number_format($order->discount_amount, 2, ',', ''),
                    number_format($order->delivery_fee, 2, ',', ''),
                    number_format($order->tax_amount, 2, ',', ''),
                    number_format($order->total, 2, ',', ''),
                    $order->payment_method,
                    $order->status_label,
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getDateRange(string $period): array
    {
        return match($period) {
            'today' => [Carbon::today(), Carbon::now()],
            'yesterday' => [Carbon::yesterday(), Carbon::yesterday()->endOfDay()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()],
            'last_month' => [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ],
            default => [Carbon::now()->startOfMonth(), Carbon::now()],
        };
    }

    private function getRestaurantId(): ?int
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return request()->get('restaurant_id');
        }

        return $user->restaurants()->first()?->id;
    }
}