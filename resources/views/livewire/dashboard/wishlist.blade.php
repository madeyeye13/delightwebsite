<div class="p-6 md:p-8 max-w-5xl mx-auto">

    <div class="mb-8">
        <h1 class="font-display text-2xl font-semibold text-white tracking-tight">Wishlist</h1>
        <p class="text-sm text-white/40 mt-1">Products you've saved</p>
    </div>

    @if($items->isEmpty())
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-14 h-14 rounded-full bg-white/[0.04] flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-white/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-white/30">Your wishlist is empty</p>
            <a href="{{ route('shop.index') }}"
               class="mt-4 text-xs text-brand-400 hover:text-brand-300 transition-colors underline underline-offset-4">
                Discover products
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($items as $item)
                @if($item->product)
                    <div class="group bg-white/[0.03] border border-white/[0.07] rounded-2xl overflow-hidden
                                hover:border-white/[0.12] transition-all duration-200"
                         wire:key="wishlist-{{ $item->id }}">

                        {{-- Image --}}
                        <a href="{{ route('products.show', $item->product->slug) }}"
                           class="block aspect-[3/4] bg-white/[0.04] overflow-hidden relative">
                            @if($item->product->thumb_image_url)
                                <img src="{{ $item->product->thumb_image_url }}"
                                     alt="{{ $item->product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500" />
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white/10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                                        <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
                                    </svg>
                                </div>
                            @endif
                            @if($item->product->status !== 'active')
                                <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                    <span class="text-xs font-medium text-white/60 tracking-wide">Unavailable</span>
                                </div>
                            @endif
                        </a>

                        {{-- Info --}}
                        <div class="p-4">
                            <p class="text-[10px] text-white/25 tracking-wider uppercase mb-1">
                                {{ $item->product->category?->name ?? '—' }}
                            </p>
                            <a href="{{ route('products.show', $item->product->slug) }}"
                               class="block text-sm font-medium text-white/80 hover:text-white truncate mb-2 transition-colors">
                                {{ $item->product->name }}
                            </a>
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-white">
                                    ₦{{ number_format($item->product->final_price) }}
                                </p>
                                <button wire:click="removeFromWishlist({{ $item->product_id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="removeFromWishlist({{ $item->product_id }})"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg
                                               text-red-400/40 hover:text-red-400 hover:bg-red-500/[0.08]
                                               transition-all duration-150 disabled:opacity-40">
                                    <svg wire:loading.remove wire:target="removeFromWishlist({{ $item->product_id }})"
                                         class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M18 6L6 18M6 6l12 12"/>
                                    </svg>
                                    <svg wire:loading wire:target="removeFromWishlist({{ $item->product_id }})"
                                         class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

</div>