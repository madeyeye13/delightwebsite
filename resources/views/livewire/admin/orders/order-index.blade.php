<div>
    {{-- Flash messages --}}
    @if(session('success'))
    <div class="mb-4 flex items-center gap-2 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-sm text-emerald-700 dark:text-emerald-400">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 flex items-center gap-2 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-400">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Bulk action bar --}}
    @if(count($selectedIds) > 0)
    <div class="mb-3 flex items-center gap-3 px-4 py-2.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
        <span class="text-sm font-medium text-red-700 dark:text-red-400">{{ count($selectedIds) }} order(s) selected</span>
        <button wire:click="deleteSelected()"
            wire:confirm="Delete {{ count($selectedIds) }} order(s)? This cannot be undone."
            class="ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a1 1 0 011-1h6a1 1 0 011 1v2"/></svg>
            Delete Selected
        </button>
        <button wire:click="$set('selectedIds', []); $set('selectAll', false)" class="text-xs text-red-500 dark:text-red-400 hover:underline">Clear</button>
    </div>
    @endif

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-5">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search order #, name, email…"
                class="w-full pl-9 pr-4 py-2 text-sm bg-white dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-white/20 focus:outline-none focus:ring-1 focus:ring-emerald-500">
        </div>
        <select wire:model.live="statusFilter"
            class="px-3 py-2 text-sm bg-white dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] rounded-xl text-gray-700 dark:text-white/80 focus:outline-none focus:ring-1 focus:ring-emerald-500">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="shipped">Shipped</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
        </select>
        <select wire:model.live="paymentFilter"
            class="px-3 py-2 text-sm bg-white dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] rounded-xl text-gray-700 dark:text-white/80 focus:outline-none focus:ring-1 focus:ring-emerald-500">
            <option value="">All Payments</option>
            <option value="pending">Unpaid</option>
            <option value="paid">Paid</option>
            <option value="refunded">Refunded</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-white/[0.06]">
                        <th class="px-4 py-3 w-8">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="rounded border-gray-300 dark:border-white/20 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-0 bg-white dark:bg-white/[0.05] cursor-pointer">
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Order</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Customer</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Shipping</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Total</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Payment</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/[0.04]">
                    @forelse($this->orders as $order)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                        <td class="px-4 py-3">
                            <input type="checkbox" wire:model.live="selectedIds" value="{{ $order->id }}"
                                class="rounded border-gray-300 dark:border-white/20 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-0 bg-white dark:bg-white/[0.05] cursor-pointer">
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono font-semibold text-emerald-600 dark:text-emerald-400 text-xs">{{ $order->order_number }}</span>
                            @if($order->isDhlOrder())
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-yellow-100 dark:bg-yellow-400/10 text-yellow-700 dark:text-yellow-400 uppercase">DHL</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $order->contact_name }}</p>
                            <p class="text-gray-400 dark:text-white/30 text-xs">{{ $order->contact_email }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-gray-700 dark:text-white/70 text-xs">{{ $order->shipping_city }}, {{ $order->shipping_country }}</p>
                            <p class="text-gray-400 dark:text-white/30 text-[10px]">{{ $order->shipping_method_name }}</p>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-semibold text-gray-900 dark:text-white">₦{{ number_format($order->total, 0) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($order->payment_status === 'paid')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Paid</span>
                            @elseif($order->payment_status === 'refunded')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-100 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400">Refunded</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <select wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                class="text-xs px-2 py-1 rounded-lg border cursor-pointer focus:outline-none focus:ring-1 focus:ring-emerald-500
                                    {{ $order->status === 'delivered'  ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400' :
                                       ($order->status === 'shipped'   ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-400' :
                                       ($order->status === 'cancelled' ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-700 dark:text-red-400' :
                                       ($order->status === 'processing'? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-400' :
                                        'bg-gray-50 dark:bg-white/[0.04] border-gray-200 dark:border-white/10 text-gray-600 dark:text-white/60'))) }}">
                                <option value="pending"    {{ $order->status === 'pending'    ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped"    {{ $order->status === 'shipped'    ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered"  {{ $order->status === 'delivered'  ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled"  {{ $order->status === 'cancelled'  ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs text-gray-400 dark:text-white/30">{{ $order->created_at->format('d M Y') }}</span>
                            <p class="text-[10px] text-gray-300 dark:text-white/20">{{ $order->created_at->format('H:i') }}</p>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button wire:click="viewOrder({{ $order->id }})"
                                class="text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 transition-colors">
                                View
                            </button>
                            <button wire:click="deleteOrder({{ $order->id }})"
                                wire:confirm="Delete order {{ $order->order_number }}? This cannot be undone."
                                class="ml-2 text-xs font-medium text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors">
                                Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-16 text-center text-gray-400 dark:text-white/30 text-sm">No orders found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($this->orders->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-white/[0.06]">
            {{ $this->orders->links() }}
        </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         Order Detail Drawer
    ═══════════════════════════════════════════════════════════════════════ --}}
    @teleport('body')
    <div>
    @if($viewing)
    <div class="fixed inset-0 z-50 flex items-start justify-end" aria-modal="true" wire:key="order-drawer">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="closeOrder()"></div>
        <div class="relative w-full max-w-lg h-full bg-white dark:bg-[#161920] border-l border-gray-100 dark:border-white/[0.06] overflow-y-auto shadow-2xl">

            {{-- Drawer header --}}
            <div class="sticky top-0 z-10 bg-white dark:bg-[#161920] border-b border-gray-100 dark:border-white/[0.06] px-5 py-4 flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-gray-900 dark:text-white">{{ $viewing->order_number }}</h2>
                    <p class="text-xs text-gray-400 dark:text-white/40">{{ $viewing->created_at->format('d M Y · H:i') }}</p>
                </div>
                <button wire:click="closeOrder()" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/[0.05] transition-colors">
                    <svg class="w-4 h-4 text-gray-500 dark:text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="px-5 py-4 space-y-5">

                {{-- Admin Actions --}}
                @if($viewing->canBeCancelled() || ($viewing->isDhlOrder() && $viewing->isPaid() && !$viewing->dhlShipment?->shipment_id))
                <div class="flex flex-wrap gap-2">
                    {{-- Create DHL Shipment button --}}
                    @if($viewing->isDhlOrder() && $viewing->isPaid() && !$viewing->dhlShipment?->shipment_id)
                    <button wire:click="openCreateShipmentModal({{ $viewing->id }})"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Create DHL Shipment
                    </button>
                    @endif

                    {{-- Cancel Order button --}}
                    @if($viewing->canBeCancelled())
                    <button wire:click="openCancelModal({{ $viewing->id }})"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-red-100 dark:bg-red-900/20 hover:bg-red-200 dark:hover:bg-red-900/40 text-red-700 dark:text-red-400 rounded-lg transition-colors border border-red-200 dark:border-red-800">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Cancel Order{{ $viewing->isPaid() ? ' & Refund' : '' }}
                    </button>
                    @endif
                </div>
                @endif

                {{-- DHL Shipment Info --}}
                @if($viewing->dhlShipment)
                <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30 rounded-xl p-4">
                    <h3 class="text-[10px] font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10l2-2zM13 6l3 4h4l-1 6h-2"/></svg>
                        DHL Shipment
                    </h3>
                    <div class="space-y-2 text-sm">
                        @if($viewing->dhlShipment->dhl_tracking_number)
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-white/40">Tracking #</span>
                            <a href="{{ $viewing->dhlShipment->trackingUrl() }}" target="_blank"
                                class="font-mono font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $viewing->dhlShipment->dhl_tracking_number }}
                            </a>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-white/40">Status</span>
                            <span class="font-medium text-gray-700 dark:text-white/70 capitalize">{{ $viewing->dhlShipment->status }}</span>
                        </div>
                        @if($viewing->dhlShipment->shipped_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-white/40">Shipped</span>
                            <span class="text-gray-700 dark:text-white/70">{{ $viewing->dhlShipment->shipped_at->format('d M Y H:i') }}</span>
                        </div>
                        @endif
                        @if($viewing->dhlShipment->estimated_delivery_date)
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-white/40">Est. Delivery</span>
                            <span class="text-gray-700 dark:text-white/70">{{ $viewing->dhlShipment->estimated_delivery_date->format('d M Y') }}</span>
                        </div>
                        @endif
                        @if($viewing->dhlShipment->label_data)
                        <div class="pt-1">
                            <a href="data:application/pdf;base64,{{ $viewing->dhlShipment->label_data }}"
                               download="DHL_Label_{{ $viewing->order_number }}.pdf"
                               class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download Shipping Label
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Customer --}}
                <div>
                    <h3 class="text-[10px] font-semibold text-gray-400 dark:text-white/30 uppercase tracking-wider mb-2">Customer</h3>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $viewing->contact_name }}</p>
                    <p class="text-sm text-gray-500 dark:text-white/50">{{ $viewing->contact_email }}</p>
                    <p class="text-sm text-gray-500 dark:text-white/50">{{ $viewing->contact_phone }}</p>
                </div>

                {{-- Shipping Address --}}
                <div>
                    <h3 class="text-[10px] font-semibold text-gray-400 dark:text-white/30 uppercase tracking-wider mb-2">Shipping Address</h3>
                    <p class="text-sm text-gray-700 dark:text-white/70">{{ $viewing->shipping_street }}{{ $viewing->shipping_house_no ? ', ' . $viewing->shipping_house_no : '' }}</p>
                    <p class="text-sm text-gray-700 dark:text-white/70">{{ $viewing->shipping_city }}, {{ $viewing->shipping_state }}</p>
                    <p class="text-sm text-gray-700 dark:text-white/70">{{ $viewing->shipping_country }}{{ $viewing->shipping_postal ? ' · ' . $viewing->shipping_postal : '' }}</p>
                    @if($viewing->shipping_notes)
                        <p class="text-sm text-gray-500 dark:text-white/40 mt-1 italic">{{ $viewing->shipping_notes }}</p>
                    @endif
                </div>

                {{-- Shipping Method --}}
                <div>
                    <h3 class="text-[10px] font-semibold text-gray-400 dark:text-white/30 uppercase tracking-wider mb-2">Shipping Method</h3>
                    <p class="text-sm text-gray-700 dark:text-white/70">{{ $viewing->shipping_method_name }}</p>
                    <p class="text-sm text-gray-500 dark:text-white/40">
                        ₦{{ number_format($viewing->shipping_cost, 0) }}
                        @if($viewing->shipping_estimated_days) · {{ $viewing->shipping_estimated_days }}d est. @endif
                    </p>
                </div>

                {{-- Items --}}
                <div>
                    <h3 class="text-[10px] font-semibold text-gray-400 dark:text-white/30 uppercase tracking-wider mb-2">Items</h3>
                    <div class="space-y-2">
                        @foreach($viewing->items as $item)
                        <div class="flex items-start gap-3 py-2 border-b border-gray-50 dark:border-white/[0.04] last:border-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item->product_name }}</p>
                                @if($item->variant_name)
                                    <p class="text-xs text-gray-400 dark:text-white/30">{{ $item->variant_name }}</p>
                                @endif
                                <p class="text-xs text-gray-400 dark:text-white/30">{{ $item->quantity }} × ₦{{ number_format($item->unit_price, 0) }}</p>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">₦{{ number_format($item->total_price, 0) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Totals --}}
                <div class="bg-gray-50 dark:bg-white/[0.03] rounded-xl p-4 space-y-2">
                    <div class="flex justify-between text-sm text-gray-500 dark:text-white/40">
                        <span>Subtotal</span><span>₦{{ number_format($viewing->subtotal, 0) }}</span>
                    </div>
                    @if($viewing->discount_amount > 0)
                    <div class="flex justify-between text-sm text-emerald-600 dark:text-emerald-400">
                        <span>Discount{{ $viewing->coupon_code ? ' (' . $viewing->coupon_code . ')' : '' }}</span>
                        <span>− ₦{{ number_format($viewing->discount_amount, 0) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-sm text-gray-500 dark:text-white/40">
                        <span>Shipping</span><span>₦{{ number_format($viewing->shipping_cost, 0) }}</span>
                    </div>
                    <div class="flex justify-between font-semibold text-gray-900 dark:text-white pt-1 border-t border-gray-200 dark:border-white/10">
                        <span>Total</span><span>₦{{ number_format($viewing->total, 0) }}</span>
                    </div>
                </div>

                {{-- Payment --}}
                <div>
                    <h3 class="text-[10px] font-semibold text-gray-400 dark:text-white/30 uppercase tracking-wider mb-2">Payment</h3>
                    <div class="flex items-center gap-2">
                        <span class="capitalize text-sm text-gray-700 dark:text-white/70">{{ $viewing->payment_method }}</span>
                        @if($viewing->payment_status === 'paid')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Paid</span>
                        @elseif($viewing->payment_status === 'refunded')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-100 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400">Refunded</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400">Unpaid</span>
                        @endif
                    </div>
                    @if($viewing->payment_reference)
                        <p class="text-xs font-mono text-gray-400 dark:text-white/30 mt-1">Ref: {{ $viewing->payment_reference }}</p>
                    @endif
                    @if($viewing->paid_at)
                        <p class="text-xs text-gray-400 dark:text-white/30 mt-0.5">Paid {{ $viewing->paid_at->format('d M Y H:i') }}</p>
                    @endif
                </div>

                {{-- Admin notes --}}
                @if($viewing->admin_notes)
                <div>
                    <h3 class="text-[10px] font-semibold text-gray-400 dark:text-white/30 uppercase tracking-wider mb-2">Admin Notes</h3>
                    <p class="text-xs text-gray-500 dark:text-white/40 whitespace-pre-line">{{ $viewing->admin_notes }}</p>
                </div>
                @endif

            </div>
        </div>
    </div>
    @endif
    </div>
    @endteleport

    {{-- ═══════════════════════════════════════════════════════════════════════
         Create DHL Shipment Modal
    ═══════════════════════════════════════════════════════════════════════ --}}
    @teleport('body')
    @if($showShipmentModal)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showShipmentModal', false)"></div>
        <div class="relative bg-white dark:bg-[#1C1F27] rounded-2xl shadow-2xl w-full max-w-md border border-gray-100 dark:border-white/[0.08] p-6">

            {{-- Title --}}
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10l2-2z"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Create DHL Shipment</h3>
                    <p class="text-xs text-gray-400 dark:text-white/40">This will book the shipment and generate a tracking number.</p>
                </div>
            </div>

            {{-- Error --}}
            @if($shipmentError)
            <div class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-400">
                {{ $shipmentError }}
            </div>
            @endif

            {{-- Phone input --}}
            <div class="mb-5">
                <label class="block text-xs font-semibold text-gray-700 dark:text-white/70 mb-1.5">
                    Receiver Phone Number <span class="text-red-500">*</span>
                </label>
                <input wire:model="shipmentPhone" type="tel" placeholder="+1234567890"
                    class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-white/[0.05] border border-gray-200 dark:border-white/[0.08] rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('shipmentPhone')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-[11px] text-gray-400 dark:text-white/30">Include country code, e.g. +2348012345678</p>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <button wire:click="$set('showShipmentModal', false)"
                    class="flex-1 px-4 py-2.5 text-sm font-medium border border-gray-200 dark:border-white/[0.08] text-gray-700 dark:text-white/70 rounded-xl hover:bg-gray-50 dark:hover:bg-white/[0.04] transition-colors">
                    Cancel
                </button>
                <button wire:click="createDhlShipment()" wire:loading.attr="disabled"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors disabled:opacity-60 flex items-center justify-center gap-2">
                    <span wire:loading wire:target="createDhlShipment" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <span>Create Shipment</span>
                </button>
            </div>
        </div>
    </div>
    @endif
    @endteleport

    {{-- ═══════════════════════════════════════════════════════════════════════
         Cancel Order Modal
    ═══════════════════════════════════════════════════════════════════════ --}}
    @teleport('body')
    @if($showCancelModal)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showCancelModal', false)"></div>
        <div class="relative bg-white dark:bg-[#1C1F27] rounded-2xl shadow-2xl w-full max-w-md border border-gray-100 dark:border-white/[0.08] p-6">

            {{-- Title --}}
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-500/10 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Cancel Order</h3>
                    @if($cancellingOrderId && ($cancelOrder = \App\Models\Order::find($cancellingOrderId)) && $cancelOrder->isPaid())
                        <p class="text-xs text-red-500 dark:text-red-400 font-medium">This order is paid — cancelling will trigger an automatic refund.</p>
                    @else
                        <p class="text-xs text-gray-400 dark:text-white/40">This action cannot be undone.</p>
                    @endif
                </div>
            </div>

            {{-- Error --}}
            @if($cancelError)
            <div class="mb-4 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-400">
                {{ $cancelError }}
            </div>
            @endif

            {{-- Reason input --}}
            <div class="mb-5">
                <label class="block text-xs font-semibold text-gray-700 dark:text-white/70 mb-1.5">
                    Cancellation Reason <span class="text-red-500">*</span>
                </label>
                <textarea wire:model="cancellationReason" rows="3"
                    placeholder="e.g. Customer requested cancellation, item out of stock…"
                    class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-white/[0.05] border border-gray-200 dark:border-white/[0.08] rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
                @error('cancellationReason')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <button wire:click="$set('showCancelModal', false)"
                    class="flex-1 px-4 py-2.5 text-sm font-medium border border-gray-200 dark:border-white/[0.08] text-gray-700 dark:text-white/70 rounded-xl hover:bg-gray-50 dark:hover:bg-white/[0.04] transition-colors">
                    Go Back
                </button>
                <button wire:click="confirmCancelOrder()" wire:loading.attr="disabled"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold bg-red-600 hover:bg-red-700 text-white rounded-xl transition-colors disabled:opacity-60 flex items-center justify-center gap-2">
                    <span wire:loading wire:target="confirmCancelOrder" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <span>Confirm Cancel</span>
                </button>
            </div>
        </div>
    </div>
    @endif
    @endteleport

</div>