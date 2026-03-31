<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(): View
    {
        return view('livewire.admin.dashboard', [
            'stats' => $this->buildStats(),
            'recentOrders' => $this->buildRecentOrders(),
            'topProducts' => $this->buildTopProducts(),
            'monthlySales' => $this->buildMonthlySales(),
        ]);
    }

    /** @return array<string, mixed> */
    private function buildStats(): array
    {
        $now = Carbon::now();

        $totalCustomers = User::where('role', 'customer')->count();
        $newThisMonth = User::where('role', 'customer')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $totalProducts = Product::active()->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');

        $thisMonthRevenue = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('total');

        $subMonth = $now->copy()->subMonth();
        $lastMonthRevenue = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', $subMonth->month)
            ->whereYear('created_at', $subMonth->year)
            ->sum('total');

        $revenueChange = $lastMonthRevenue > 0
            ? (int) round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100)
            : 0;

        return [
            'total_customers' => $totalCustomers,
            'new_customers_month' => $newThisMonth,
            'total_products' => $totalProducts,
            'total_orders' => $totalOrders,
            'total_revenue' => (int) $totalRevenue,
            'this_month_revenue' => (int) $thisMonthRevenue,
            'revenue_change' => $revenueChange,
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function buildRecentOrders(): array
    {
        return Order::with('user')
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (Order $order) => [
                'order_number' => $order->order_number,
                'customer_name' => $order->contact_name ?? $order->user?->name ?? 'Guest',
                'date' => $order->created_at->format('d M Y'),
                'amount' => number_format((int) $order->total),
                'status' => $order->status,
                'payment_status' => $order->payment_status,
            ])
            ->toArray();
    }

    /** @return array<int, array<string, mixed>> */
    private function buildTopProducts(): array
    {
        $rows = OrderItem::query()
            ->select('product_name', DB::raw('SUM(quantity) as total_sold'))
            ->where('is_addon', false)
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $max = $rows->max('total_sold') ?: 1;

        return $rows
            ->map(fn ($row) => [
                'product_name' => $row->product_name,
                'total_sold' => (int) $row->total_sold,
                'bar_pct' => (int) round(($row->total_sold / $max) * 100),
            ])
            ->toArray();
    }

    /** @return array<int, array<string, mixed>> */
    private function buildMonthlySales(): array
    {
        $dbRows = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn ($row) => $row->year.'-'.str_pad((string) $row->month, 2, '0', STR_PAD_LEFT));

        $months = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i)->startOfMonth();
            $key = $date->year.'-'.$date->format('m');
            $months->push([
                'label' => $date->format('M'),
                'revenue' => $dbRows->has($key) ? (int) $dbRows[$key]->revenue : 0,
            ]);
        }

        $max = $months->max('revenue') ?: 1;

        return $months
            ->map(fn ($row) => array_merge($row, [
                'bar_pct' => (int) round(($row['revenue'] / $max) * 100),
            ]))
            ->toArray();
    }
}
