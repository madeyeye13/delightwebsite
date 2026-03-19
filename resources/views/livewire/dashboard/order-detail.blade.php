<div class="p-6 md:p-8 max-w-4xl mx-auto"
     x-data="{
         showCancelModal:  false,
         showAddressModal: false,
         showToast:        false,
         toastMsg:         '',
         toastType:        'success',

         toast(msg, type = 'success') {
             this.toastMsg  = msg;
             this.toastType = type;
             this.showToast = true;
             setTimeout(() => this.showToast = false, 4000);
         }
     }"
     @order-cancelled.window="showCancelModal = false; toast('Order cancelled. Refund is being processed.', 'success')"
     @address-updated.window="showAddressModal = false; toast('Delivery address updated successfully.', 'success')"
>

    {{-- ── Toast notification ── --}}
    <div x-show="showToast" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         :class="toastType === 'success' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300'
                                         : 'border-red-500/30 bg-red-500/10 text-red-300'"
         class="fixed top-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-xl border
                text-sm font-medium shadow-xl max-w-sm">
        <svg x-show="toastType === 'success'" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
        <svg x-show="toastType === 'error'" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
        <span x-text="toastMsg"></span>
    </div>

    {{-- ── Cancel Confirmation Modal ── --}}
    <div x-show="showCancelModal" x-cloak
         class="fixed inset-0 z-40 flex items-center justify-center p-4">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"
             @click="showCancelModal = false"></div>

        {{-- Modal panel --}}
        <div class="relative bg-[#161a22] border border-white/[0.1] rounded-2xl
                    w-full max-w-sm p-6 shadow-2xl z-10"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <div class="w-10 h-10 rounded-full bg-red-500/10 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/>
                </svg>
            </div>

            <h3 class="font-display text-base font-semibold text-white mb-1">Cancel this order?</h3>
            <p class="text-sm text-white/50 mb-6 leading-relaxed">
                Order <span class="text-white/80 font-medium">#{{ $order->order_number }}</span> will be cancelled.
                @if($order->payment_status === 'paid')
                    A full refund of <span class="text-white/80 font-medium">₦{{ number_format($order->total) }}</span>
                    will be processed automatically.
                @endif
            </p>

            @error('cancel')
                <p class="text-xs text-red-400 mb-4 bg-red-500/10 border border-red-500/20 rounded-lg px-3 py-2">
                    {{ $message }}
                </p>
            @enderror

            <div class="flex items-center gap-3">
                <button wire:click="cancelOrder" wire:loading.attr="disabled"
                        class="flex-1 flex items-center justify-center gap-2 bg-red-500/10 hover:bg-red-500/20
                               border border-red-500/30 text-red-400 hover:text-red-300
                               text-sm font-medium rounded-xl py-2.5 transition-all duration-150
                               disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg wire:loading wire:target="cancelOrder" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                    </svg>
                    <span wire:loading.remove wire:target="cancelOrder">Yes, cancel order</span>
                    <span wire:loading wire:target="cancelOrder">Processing…</span>
                </button>
                <button @click="showCancelModal = false"
                        class="flex-1 bg-white/[0.05] hover:bg-white/[0.08] border border-white/[0.08]
                               text-white/60 hover:text-white text-sm font-medium
                               rounded-xl py-2.5 transition-all duration-150">
                    Keep order
                </button>
            </div>
        </div>
    </div>

    {{-- ── Address Change Modal ── --}}
    <div x-show="showAddressModal" x-cloak
         class="fixed inset-0 z-40 flex items-center justify-center p-4">

        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"
             @click="showAddressModal = false"></div>

        <div class="relative bg-[#161a22] border border-white/[0.1] rounded-2xl
                    w-full max-w-lg p-6 shadow-2xl z-10 max-h-[90vh] overflow-y-auto"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <div class="flex items-center justify-between mb-5">
                <h3 class="font-display text-base font-semibold text-white">Update delivery address</h3>
                <button @click="showAddressModal = false"
                        class="w-7 h-7 flex items-center justify-center rounded-lg
                               text-white/30 hover:text-white hover:bg-white/[0.07] transition-all">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @error('address')
                <div class="text-xs text-red-400 mb-4 bg-red-500/10 border border-red-500/20 rounded-lg px-3 py-2">
                    {{ $message }}
                </div>
            @enderror

            <div class="space-y-4">
                {{-- Street --}}
                <div>
                    <label class="block text-[11px] font-medium tracking-widest uppercase text-white/40 mb-1.5">
                        Street Address *
                    </label>
                    <input wire:model="street" type="text" placeholder="12 Marina Street"
                           class="w-full bg-white/[0.04] border border-white/[0.08] rounded-xl
                                  text-sm text-white placeholder-white/20 px-4 py-2.5
                                  focus:outline-none focus:border-brand-500/50 transition-colors" />
                    @error('street')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- City + State --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-medium tracking-widest uppercase text-white/40 mb-1.5">City *</label>
                        <input wire:model="city" type="text" placeholder="Lagos"
                               class="w-full bg-white/[0.04] border border-white/[0.08] rounded-xl
                                      text-sm text-white placeholder-white/20 px-4 py-2.5
                                      focus:outline-none focus:border-brand-500/50 transition-colors" />
                        @error('city')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium tracking-widest uppercase text-white/40 mb-1.5">State *</label>
                        <input wire:model="state" type="text" placeholder="Lagos State"
                               class="w-full bg-white/[0.04] border border-white/[0.08] rounded-xl
                                      text-sm text-white placeholder-white/20 px-4 py-2.5
                                      focus:outline-none focus:border-brand-500/50 transition-colors" />
                        @error('state')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Country + Postal --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-medium tracking-widest uppercase text-white/40 mb-1.5">Country *</label>
                        <input wire:model="country" type="text" placeholder="NG" maxlength="2"
                               class="w-full bg-white/[0.04] border border-white/[0.08] rounded-xl
                                      text-sm text-white placeholder-white/20 px-4 py-2.5
                                      focus:outline-none focus:border-brand-500/50 transition-colors uppercase" />
                        @error('country')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium tracking-widests uppercase text-white/40 mb-1.5">Postal Code</label>
                        <input wire:model="postal" type="text" placeholder="100001"
                               class="w-full bg-white/[0.04] border border-white/[0.08] rounded-xl
                                      text-sm text-white placeholder-white/20 px-4 py-2.5
                                      focus:outline-none focus:border-brand-500/50 transition-colors" />
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-[11px] font-medium tracking-widests uppercase text-white/40 mb-1.5">Delivery Notes</label>
                    <textarea wire:model="notes" rows="2" placeholder="Any instructions for the courier…"
                              class="w-full bg-white/[0.04] border border-white/[0.08] rounded-xl
                                     text-sm text-white placeholder-white/20 px-4 py-2.5 resize-none
                                     focus:outline-none focus:border-brand-500/50 transition-colors"></textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button wire:click="updateAddress" wire:loading.attr="disabled"
                        class="flex-1 flex items-center justify-center gap-2
                               bg-brand-500/10 hover:bg-brand-500/20 border border-brand-500/30
                               text-brand-400 hover:text-brand-300 text-sm font-medium
                               rounded-xl py-2.5 transition-all duration-150
                               disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg wire:loading wire:target="updateAddress" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                    </svg>
                    <span wire:loading.remove wire:target="updateAddress">Save address</span>
                    <span wire:loading wire:target="updateAddress">Saving…</span>
                </button>
                <button @click="showAddressModal = false"
                        class="flex-1 bg-white/[0.05] hover:bg-white/[0.08] border border-white/[0.08]
                               text-white/60 hover:text-white text-sm font-medium
                               rounded-xl py-2.5 transition-all duration-150">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    {{-- ── Back link ── --}}
    <div class="mb-6">
        <a href="{{ route('account.orders') }}"
           class="inline-flex items-center gap-2 text-white/40 hover:text-white text-xs
                  font-medium tracking-wide transition-colors group">
            <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            Back to Orders
        </a>
    </div>

    {{-- ── Order header card ── --}}
    <div class="bg-white/[0.03] border border-white/[0.07] rounded-2xl p-5 mb-4">
        <div class="flex items-start justify-between gap-4 flex-wrap mb-4">
            <div>
                <div class="flex items-center gap-2.5 mb-1 flex-wrap">
                    <h1 class="font-display text-xl font-semibold text-white tracking-tight">
                        #{{ $order->order_number }}
                    </h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-semibold tracking-wider uppercase {{ $order->statusColor() }}">
                        {{ $order->statusLabel() }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-medium {{ $order->paymentStatusColor() }}">
                        {{ $order->paymentStatusLabel() }}
                    </span>
                </div>
                <p class="text-xs text-white/30">Placed {{ $order->created_at->format('d M Y, g:ia') }}</p>
            </div>

            {{-- Action buttons --}}
            <div class="flex items-center gap-2 flex-wrap">
                @if($order->canChangeAddress())
                    <button @click="showAddressModal = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl
                                   bg-white/[0.04] hover:bg-white/[0.08] border border-white/[0.08]
                                   text-xs font-medium text-white/60 hover:text-white
                                   transition-all duration-150">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                        </svg>
                        Change Address
                    </button>
                @endif

                @if($order->canBeCancelled())
                    <button @click="showCancelModal = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl
                                   bg-red-500/[0.07] hover:bg-red-500/[0.14] border border-red-500/20
                                   text-xs font-medium text-red-400/70 hover:text-red-400
                                   transition-all duration-150">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/>
                        </svg>
                        Cancel Order
                    </button>
                @endif
            </div>
        </div>

        {{-- Shipping info --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-white/[0.06]">
            <div>
                <p class="text-[10px] font-medium tracking-widest uppercase text-white/25 mb-1">Delivery Address</p>
                <p class="text-sm text-white/70 leading-relaxed">
                    {{ $order->shipping_street }}<br>
                    {{ $order->shipping_city }}@if($order->shipping_state), {{ $order->shipping_state }}@endif<br>
                    {{ $order->shipping_country }}@if($order->shipping_postal) {{ $order->shipping_postal }}@endif
                </p>
                @if($order->shipping_notes)
                    <p class="text-xs text-white/30 mt-1 italic">{{ $order->shipping_notes }}</p>
                @endif
            </div>
            <div>
                <p class="text-[10px] font-medium tracking-widests uppercase text-white/25 mb-1">Shipping Method</p>
                <p class="text-sm text-white/70">{{ $order->shipping_method_name ?? ucfirst($order->shipping_carrier ?? 'Standard') }}</p>
                @if($order->shipping_estimated_days)
                    <p class="text-xs text-white/30 mt-0.5">Est. {{ $order->shipping_estimated_days }} business days</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Order items ── --}}
    <div class="bg-white/[0.03] border border-white/[0.07] rounded-2xl overflow-hidden mb-4">
        <div class="px-5 py-3.5 border-b border-white/[0.06]">
            <p class="text-xs font-semibold text-white/50 tracking-wide">
                {{ $order->items->count() }} {{ Str::plural('Item', $order->items->count()) }}
            </p>
        </div>

        <div class="divide-y divide-white/[0.04]">
            @foreach($order->items as $item)
                <div class="px-5 py-4 flex items-start gap-4">
                    {{-- Product thumb --}}
                    <div class="w-12 h-14 rounded-lg bg-white/[0.05] overflow-hidden shrink-0">
                        @if($item->product?->thumb_image_url)
                            <img src="{{ $item->product->thumb_image_url }}" alt="{{ $item->product_name }}"
                                 class="w-full h-full object-cover" />
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white/15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white/80 truncate">{{ $item->product_name }}</p>
                        @if($item->variant_name)
                            <p class="text-xs text-white/40 mt-0.5">{{ $item->variant_name }}</p>
                        @endif
                        <p class="text-xs text-white/30 mt-0.5">
                            {{ $item->quantity }} {{ $item->unit_label ?: 'unit' }}
                            @if($item->units_per_order > 1) × {{ $item->units_per_order }} per order @endif
                        </p>
                    </div>

                    <div class="text-right shrink-0">
                        <p class="text-sm font-medium text-white/80">₦{{ number_format($item->total_price) }}</p>
                        <p class="text-xs text-white/30 mt-0.5">₦{{ number_format($item->unit_price) }} each</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Order totals ── --}}
    <div class="bg-white/[0.03] border border-white/[0.07] rounded-2xl p-5">
        <div class="space-y-2.5">
            <div class="flex justify-between text-sm">
                <span class="text-white/40">Subtotal</span>
                <span class="text-white/70">₦{{ number_format($order->subtotal) }}</span>
            </div>
            @if($order->discount_amount > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-white/40">Coupon discount</span>
                    <span class="text-emerald-400">−₦{{ number_format($order->discount_amount) }}</span>
                </div>
            @endif
            @if($order->referral_discount_amount > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-white/40">Referral discount</span>
                    <span class="text-emerald-400">−₦{{ number_format($order->referral_discount_amount) }}</span>
                </div>
            @endif
            @if($order->points_discount_amount > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-white/40">Reward points ({{ $order->points_redeemed }} pts)</span>
                    <span class="text-emerald-400">−₦{{ number_format($order->points_discount_amount) }}</span>
                </div>
            @endif
            <div class="flex justify-between text-sm">
                <span class="text-white/40">Shipping</span>
                <span class="text-white/70">
                    {{ $order->shipping_cost > 0 ? '₦'.number_format($order->shipping_cost) : 'Free' }}
                </span>
            </div>
            <div class="flex justify-between text-sm font-semibold pt-2.5 border-t border-white/[0.07]">
                <span class="text-white">Total</span>
                <span class="text-white">₦{{ number_format($order->total) }}</span>
            </div>
        </div>
    </div>

</div>