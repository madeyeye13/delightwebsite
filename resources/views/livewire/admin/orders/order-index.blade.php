<div>
    @if(session('success'))
    <div class="mb-4 flex items-center gap-2 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-sm text-emerald-700 dark:text-emerald-400">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
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

    {{-- Filters row --}}
    <div class="flex flex-wrap items-center gap-3 mb-5">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.400ms="search"
                type="text"
                placeholder="Search order #, name, email…"
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
                                    {{ $order->status === 'delivered' ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400' :
                                       ($order->status === 'shipped'  ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-400' :
                                       ($order->status === 'cancelled'? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-700 dark:text-red-400' :
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
                        <td class="px-4 py-3">
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
                        <td colspan="9" class="px-4 py-16 text-center text-gray-400 dark:text-white/30 text-sm">
                            No orders found.
                        </td>
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

    {{-- Order Detail Drawer --}}
    @teleport('body')
    <div>
    @if($viewing)
    <div class="fixed inset-0 z-50 flex items-start justify-end" aria-modal="true" wire:key="order-drawer">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="closeOrder()"></div>
        <div class="relative w-full max-w-lg h-full bg-white dark:bg-[#161920] border-l border-gray-100 dark:border-white/[0.06] overflow-y-auto shadow-2xl">
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
                    <p class="text-sm text-gray-500 dark:text-white/40">₦{{ number_format($viewing->shipping_cost, 0) }}
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

            </div>
        </div>
    </div>
    @endif
    </div>
    @endteleport
</div>
