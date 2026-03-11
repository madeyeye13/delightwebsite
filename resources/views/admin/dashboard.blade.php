@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <span class="text-xs text-gray-400 dark:text-white/30">Home</span>
    <svg class="w-3 h-3 text-gray-300 dark:text-white/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="text-xs text-emerald-500 font-medium">Dashboard</span>
@endsection

@section('content')

    {{-- Welcome row --}}
    <div class="flex items-center justify-between mb-7">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white font-display">
                Hello, {{ auth()->user()->name ?? 'Admin' }} 👋
            </h2>
            <p class="text-sm text-gray-400 dark:text-white/40 mt-0.5">Here's what's happening with your store today.</p>
        </div>
        <div class="hidden sm:flex items-center gap-2">
            <span class="text-xs text-gray-400 dark:text-white/30">{{ now()->format('l, d M Y') }}</span>
        </div>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-7">
        @foreach([
            ['Total Customers', '2,000+', '+12% this month', 'up', '#34d399',
                '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>'],
            ['Total Products', '140+', '+5 added this week', 'up', '#60a5fa',
                '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>'],
            ['Total Orders', '1,600+', '+8% vs last week', 'up', '#f59e0b',
                '<path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/>'],
            ['Total Revenue', '₦2M+', '+18% this month', 'up', '#a78bfa',
                '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
        ] as [$label, $value, $sub, $dir, $color, $icon])
        <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-5 flex flex-col gap-3 hover:shadow-md dark:hover:shadow-black/20 transition-shadow">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: {{ $color }}18">
                    <svg class="w-5 h-5" style="color: {{ $color }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">{!! $icon !!}</svg>
                </div>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background: {{ $color }}18; color: {{ $color }}">
                    {{ $dir === 'up' ? '↑' : '↓' }} {{ explode(' ', $sub)[0] }}
                </span>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white font-display leading-none">{{ $value }}</p>
                <p class="text-xs text-gray-400 dark:text-white/40 mt-1">{{ $label }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── PLACEHOLDER CHARTS ROW ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-7">
        {{-- Sales trend placeholder --}}
        <div class="lg:col-span-2 bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Sales Trend</h3>
                    <p class="text-xs text-gray-400 dark:text-white/30 mt-0.5">Revenue overview for this year</p>
                </div>
                <div class="flex items-center gap-3 text-xs text-gray-400 dark:text-white/30">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-400 inline-block"></span>Current</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-300 dark:bg-white/20 inline-block"></span>Last year</span>
                </div>
            </div>
            <div class="h-44 flex items-center justify-center rounded-xl bg-gray-50 dark:bg-white/[0.02] border border-dashed border-gray-200 dark:border-white/[0.06]">
                <p class="text-xs text-gray-300 dark:text-white/20">Chart goes here (e.g. Chart.js / ApexCharts)</p>
            </div>
        </div>

        {{-- Top products placeholder --}}
        <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Top Products</h3>
                <a href="{{ route('admin.products.index') }}" class="text-xs text-emerald-500 hover:text-emerald-400 transition-colors">View all</a>
            </div>
            <div class="space-y-3">
                @foreach([
                    ['Ankara Print Fabric', '75%', 'bg-emerald-400'],
                    ['Silk Chiffon', '90%', 'bg-blue-400'],
                    ['Cotton Linen', '60%', 'bg-amber-400'],
                    ['Velvet Fabric', '45%', 'bg-purple-400'],
                ] as [$name, $pct, $color])
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-600 dark:text-white/60">{{ $name }}</span>
                        <span class="font-medium text-gray-800 dark:text-white/80">{{ $pct }}</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 dark:bg-white/[0.06] rounded-full overflow-hidden">
                        <div class="{{ $color }} h-full rounded-full" style="width: {{ $pct }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── RECENT ORDERS TABLE ── --}}
    <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-white/[0.06]">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Recent Orders</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-emerald-500 hover:text-emerald-400 transition-colors flex items-center gap-1">
                View all
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 dark:border-white/[0.04]">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-white/30 uppercase tracking-wider">Order ID</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-white/30 uppercase tracking-wider">Customer</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-white/30 uppercase tracking-wider hidden md:table-cell">Date</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-white/30 uppercase tracking-wider hidden sm:table-cell">Amount</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 dark:text-white/30 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/[0.03]">
                    @foreach([
                        ['#20241', 'Amara Okafor', '12 Mar 2025', '₦18,500', 'Completed'],
                        ['#20240', 'Chidinma Eze', '11 Mar 2025', '₦7,200', 'Pending'],
                        ['#20239', 'Emeka Nwosu', '11 Mar 2025', '₦32,000', 'Completed'],
                        ['#20238', 'Fatima Bello', '10 Mar 2025', '₦5,500', 'Processing'],
                        ['#20237', 'Kelechi Obi', '10 Mar 2025', '₦14,750', 'Cancelled'],
                    ] as [$id, $name, $date, $amount, $status])
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                        <td class="px-5 py-3.5">
                            <span class="text-xs font-mono font-medium text-emerald-500">{{ $id }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-emerald-400/20 to-teal-600/20 flex items-center justify-center text-emerald-500 text-xs font-semibold shrink-0">
                                    {{ strtoupper(substr($name, 0, 1)) }}
                                </div>
                                <span class="text-sm text-gray-700 dark:text-white/70 font-medium">{{ $name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 hidden md:table-cell text-xs text-gray-400 dark:text-white/30">{{ $date }}</td>
                        <td class="px-5 py-3.5 hidden sm:table-cell text-sm font-semibold text-gray-800 dark:text-white/80">{{ $amount }}</td>
                        <td class="px-5 py-3.5">
                            @php
                                $statusColors = [
                                    'Completed'  => 'bg-emerald-500/10 text-emerald-500',
                                    'Pending'    => 'bg-amber-500/10 text-amber-500',
                                    'Processing' => 'bg-blue-500/10 text-blue-500',
                                    'Cancelled'  => 'bg-red-500/10 text-red-400',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$status] ?? '' }}">
                                {{ $status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection