<div class="p-6 md:p-8 max-w-5xl mx-auto">
    <!---resources/views/livewire/dashboard/orders.blade.php--->
    {{-- Page header --}}
    <div class="mb-8">
        <h1 class="font-display text-2xl font-semibold text-white tracking-tight">My Orders</h1>
        <p class="text-sm text-white/40 mt-1">Track and manage your purchases</p>
    </div>

    {{-- Status filter tabs --}}
    <div class="flex items-center gap-1 mb-6 flex-wrap"
         x-data>
        @foreach([
            'all'        => 'All',
            'pending'    => 'Pending',
            'processing' => 'Processing',
            'shipped'    => 'Shipped',
            'delivered'  => 'Delivered',
            'cancelled'  => 'Cancelled',
        ] as $value => $label)
            <button
                wire:click="$set('statusFilter', '{{ $value }}')"
                class="px-3.5 py-1.5 rounded-md text-xs font-medium tracking-wide transition-all duration-150
                       {{ $statusFilter === $value
                           ? 'bg-brand-500/15 text-brand-400 border border-brand-500/30'
                           : 'text-white/40 hover:text-white/70 border border-transparent hover:border-white/10' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Orders list --}}
    @if($orders->isEmpty())
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-14 h-14 rounded-full bg-white/[0.04] flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                    <rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-white/30">No orders found</p>
            <a href="{{ route('shop.index') }}"
               class="mt-4 text-xs text-brand-400 hover:text-brand-300 transition-colors underline underline-offset-4">
                Browse our shop
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach($orders as $order)
                <a href="{{ route('account.orders.show', $order) }}"
                   class="group block bg-white/[0.03] border border-white/[0.07] rounded-xl
                          hover:border-white/[0.14] hover:bg-white/[0.05]
                          transition-all duration-200 p-5">

                    <div class="flex items-start justify-between gap-4 flex-wrap">

                        {{-- Left: order info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2.5 mb-2 flex-wrap">
                                <span class="font-display text-sm font-semibold text-white tracking-wide">
                                    #{{ $order->order_number }}
                                </span>
                                {{-- Status badge --}}
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold tracking-wider uppercase {{ $order->statusColor() }}">
                                    {{ $order->statusLabel() }}
                                </span>
                                {{-- Payment badge --}}
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium {{ $order->paymentStatusColor() }}">
                                    {{ $order->paymentStatusLabel() }}
                                </span>
                            </div>

                            {{-- Items summary --}}
                            <p class="text-xs text-white/40 mb-1 truncate">
                                {{ $order->items->take(2)->pluck('product_name')->join(', ') }}
                                @if($order->items->count() > 2)
                                    + {{ $order->items->count() - 2 }} more
                                @endif
                            </p>

                            <p class="text-[11px] text-white/25">
                                {{ $order->created_at->format('d M Y') }}
                                · {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                                · via {{ ucfirst($order->shipping_carrier ?? 'Standard') }}
                            </p>
                        </div>

                        {{-- Right: total + arrow --}}
                        <div class="flex items-center gap-3 shrink-0">
                            <div class="text-right">
                                <p class="text-sm font-semibold text-white">
                                    ₦{{ number_format($order->total) }}
                                </p>
                            </div>
                            <svg class="w-4 h-4 text-white/20 group-hover:text-white/50 transition-colors"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 18l6-6-6-6"/>
                            </svg>
                        </div>

                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif

</div>