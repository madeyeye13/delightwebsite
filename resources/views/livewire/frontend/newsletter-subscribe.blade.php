<div>
    @if($subscribed)
        <div class="flex items-center gap-2.5 text-sm text-emerald-400">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>You're subscribed — welcome!</span>
        </div>
    @else
        <form wire:submit="subscribe">
            <div class="flex gap-0">
                <label for="footer-newsletter-email" class="sr-only">Email address</label>
                <input
                    id="footer-newsletter-email"
                    type="email"
                    wire:model="email"
                    placeholder="Your email address"
                    class="flex-1 min-w-0 px-3.5 py-2.5 text-sm appearance-none bg-white/[0.08] border border-white/20 border-r-0 rounded-l text-white placeholder-white/35 focus:outline-none focus:border-white/40 focus:bg-white/[0.12] transition-colors duration-200 disabled:opacity-50"
                    autocomplete="email"
                >
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="subscribe"
                    class="shrink-0 px-4 py-2.5 bg-brand-500 text-white text-xs font-semibold tracking-wide rounded-r hover:bg-brand-400 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    aria-label="Subscribe to newsletter"
                >
                    <span wire:loading.remove wire:target="subscribe">Subscribe</span>
                    <span wire:loading wire:target="subscribe" style="display:none">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </span>
                </button>
            </div>
            @error('email')
                <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
            @enderror
            <p class="mt-3 text-xs text-white/30">No spam. Unsubscribe anytime.</p>
        </form>
    @endif
</div>
