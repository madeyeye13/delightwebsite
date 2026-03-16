{{-- livewire/frontend/wishlist-toggle.blade.php --}}
<button
    wire:click="toggle"
    class="inline-flex items-center gap-1.5 font-sans text-xs transition-colors
           {{ $wishlisted
               ? 'text-brand dark:text-brand-300'
               : 'text-neutral-400 dark:text-neutral-500 hover:text-brand dark:hover:text-brand-300' }}"
    aria-label="{{ $wishlisted ? 'Remove from wishlist' : 'Add to Wishlist' }}"
>
    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4 flex-shrink-0">
        <path
            d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"
            stroke="currentColor"
            stroke-width="1.3"
            fill="{{ $wishlisted ? 'currentColor' : 'none' }}"
        />
    </svg>
    <span wire:loading.remove wire:target="toggle">
        {{ $wishlisted ? 'Wishlisted' : 'Add to Wishlist' }}
    </span>
    <span wire:loading wire:target="toggle">...</span>
</button>
