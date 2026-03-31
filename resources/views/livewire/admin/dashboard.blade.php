<div>
    {{-- Welcome row --}}
    <div class="flex items-center justify-between mb-7">
        <div>
            <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-50 font-display">
                Hello, {{ auth()->user()->name ?? 'Admin' }} 👋
            </h2>
            <p class="text-sm text-neutral-400 dark:text-neutral-500 mt-0.5">Here's what's happening with your store today.</p>
        </div>
        <div class="hidden sm:flex items-center gap-2">
            <span class="text-xs text-neutral-400 dark:text-neutral-500">{{ now()->format('l, d M Y') }}</span>
        </div>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-7">

        {{-- Total Customers --}}
        <div class="bg-neutral-50 dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 rounded-lg p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: #34d39918">
                    <svg class="w-5 h-5" style="color: #34d399" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                @if($stats['new_customers_month'] > 0)
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background: #34d39918; color: #34d399">
                        +{{ $stats['new_customers_month'] }} this month
                    </span>
                @endif
            </div>
            <div>
                <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-50 font-display leading-none">{{ number_format($stats['total_customers']) }}</p>
                <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Total Customers</p>
            </div>
        </div>

        {{-- Total Products --}}
        <div class="bg-neutral-50 dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 rounded-lg p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: #60a5fa18">
                    <svg class="w-5 h-5" style="color: #60a5fa" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>
                    </svg>
                </div>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background: #60a5fa18; color: #60a5fa">
                    Active
                </span>
            </div>
            <div>
                <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-50 font-display leading-none">{{ number_format($stats['total_products']) }}</p>
                <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Total Products</p>
            </div>
        </div>

        {{-- Total Orders --}}
        <div class="bg-neutral-50 dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 rounded-lg p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: #f59e0b18">
                    <svg class="w-5 h-5" style="color: #f59e0b" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/>
                    </svg>
                </div>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background: #f59e0b18; color: #f59e0b">
                    All time
                </span>
            </div>
            <div>
                <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-50 font-display leading-none">{{ number_format($stats['total_orders']) }}</p>
                <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Total Orders</p>
            </div>
        </div>

        {{-- Total Revenue --}}
        <div class="bg-neutral-50 dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 rounded-lg p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: #a78bfa18">
                    <svg class="w-5 h-5" style="color: #a78bfa" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                @if($stats['revenue_change'] !== 0)
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background: #a78bfa18; color: #a78bfa">
                        {{ $stats['revenue_change'] > 0 ? '↑' : '↓' }} {{ abs($stats['revenue_change']) }}% this month
                    </span>
                @endif
            </div>
            <div>
                <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-50 font-display leading-none">₦{{ number_format($stats['total_revenue']) }}</p>
                <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Total Revenue</p>
            </div>
        </div>

    </div>

    {{-- ── CHARTS ROW ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-7">

        {{-- Monthly Sales Bar Chart --}}
        <div class="lg:col-span-2 bg-neutral-50 dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 rounded-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-neutral-50">Sales Trend</h3>
                    <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">Revenue for the last 6 months</p>
                </div>
                <div class="text-xs text-neutral-400 dark:text-neutral-500">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-400 inline-block"></span>Revenue</span>
                </div>
            </div>

            @if(count($monthlySales) > 0)
                <div class="flex items-end gap-1.5 h-36">
                    @foreach($monthlySales as $month)
                        @php $barH = max(4, (int)round($month['bar_pct'] * 1.1)); @endphp
                        <div class="flex-1 flex flex-col items-center justify-end gap-1 group">
                            <div
                                class="w-full bg-emerald-400 hover:bg-emerald-500 rounded-t transition-colors cursor-default"
                                style="height: {{ $barH }}px"
                                title="₦{{ number_format($month['revenue']) }}"
                            ></div>
                            <span class="text-2xs text-gray-400 dark:text-white/30">{{ $month['label'] }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-2">
                    This month: <span class="font-semibold text-neutral-700 dark:text-neutral-300">₦{{ number_format($stats['this_month_revenue']) }}</span>
                </p>
            @else
                    <div class="h-44 flex items-center justify-center rounded-lg bg-neutral-100 dark:bg-neutral-800/30 border border-dashed border-neutral-300 dark:border-neutral-700">
                    <p class="text-xs text-neutral-400 dark:text-neutral-600">No paid orders yet</p>
                </div>
            @endif
        </div>

        {{-- Top Products --}}
        <div class="bg-neutral-50 dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 rounded-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-neutral-900 dark:text-neutral-50">Top Products</h3>
                <a href="{{ route('admin.products.index') }}" class="text-xs text-emerald-500 hover:text-emerald-400 transition-colors">View all</a>
            </div>

            @if(count($topProducts) > 0)
                <div class="space-y-3">
                    @foreach($topProducts as $product)
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-neutral-600 dark:text-neutral-400 truncate pr-2">{{ $product['product_name'] }}</span>
                                <span class="font-medium text-neutral-800 dark:text-neutral-200 flex-shrink-0">{{ number_format($product['total_sold']) }}</span>
                            </div>
                            <div class="h-1.5 bg-neutral-200 dark:bg-neutral-800 rounded-full overflow-hidden">
                                @php
                                    $colors = ['bg-emerald-400', 'bg-blue-400', 'bg-amber-400', 'bg-purple-400', 'bg-rose-400'];
                                    $color  = $colors[$loop->index % count($colors)];
                                @endphp
                                <div class="{{ $color }} h-full rounded-full" style="width: {{ $product['bar_pct'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex items-center justify-center h-24">
                    <p class="text-xs text-neutral-400 dark:text-neutral-500">No sales data yet</p>
                </div>
            @endif
        </div>

    </div>

    {{-- ── RECENT ORDERS TABLE ── --}}
    <div class="bg-neutral-50 dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 rounded-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-200 dark:border-neutral-800">
            <h3 class="text-sm font-semibold text-neutral-900 dark:text-neutral-50">Recent Orders</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-emerald-500 hover:text-emerald-400 transition-colors flex items-center gap-1">
                View all
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>

        @if(count($recentOrders) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-neutral-100 dark:border-neutral-800">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Order ID</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Customer</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider hidden md:table-cell">Date</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider hidden sm:table-cell">Amount</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @foreach($recentOrders as $order)
                            @php
                                $statusColors = [
                                    'pending'    => 'bg-amber-500/10 text-amber-500',
                                    'processing' => 'bg-blue-500/10 text-blue-500',
                                    'shipped'    => 'bg-indigo-500/10 text-indigo-500',
                                    'delivered'  => 'bg-emerald-500/10 text-emerald-500',
                                    'completed'  => 'bg-emerald-500/10 text-emerald-500',
                                    'cancelled'  => 'bg-red-500/10 text-red-400',
                                ];
                                $sc = $statusColors[$order['status']] ?? 'bg-neutral-100 dark:bg-neutral-800 text-neutral-500';
                            @endphp
                            <tr class="hover:bg-neutral-100 dark:hover:bg-neutral-800/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span class="text-xs font-mono font-medium text-emerald-500">{{ $order['order_number'] }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-emerald-400/20 to-teal-600/20 flex items-center justify-center text-emerald-500 text-xs font-semibold shrink-0">
                                            {{ strtoupper(substr($order['customer_name'], 0, 1)) }}
                                        </div>
                                        <span class="text-sm text-neutral-700 dark:text-neutral-300 font-medium">{{ $order['customer_name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 hidden md:table-cell text-xs text-neutral-400 dark:text-neutral-500">{{ $order['date'] }}</td>
                                <td class="px-5 py-3.5 hidden sm:table-cell text-sm font-semibold text-neutral-800 dark:text-neutral-200">₦{{ $order['amount'] }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sc }}">
                                        {{ ucfirst($order['status']) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="flex items-center justify-center py-12">
                <p class="text-sm text-neutral-400 dark:text-neutral-500">No orders yet</p>
            </div>
        @endif
    </div>
</div>
