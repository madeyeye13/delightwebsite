<div>
    @if($submitted)
        <div class="flex items-start gap-4 p-6 bg-brand-50 dark:bg-brand-900 border border-brand-200 dark:border-brand-700 rounded-sm">
            <div class="w-9 h-9 flex-shrink-0 bg-brand rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-ink dark:text-neutral-100 mb-1">Message sent!</p>
                <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                    Thank you for reaching out. We'll get back to you within 24–48 hours.
                </p>
            </div>
        </div>

    @else
        <form wire:submit="submit" novalidate>
            {{-- Name + Email --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <div>
                    <label for="contact-name" class="block text-xs font-semibold text-neutral-600 dark:text-neutral-400 uppercase tracking-wide mb-1.5">
                        Full Name <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <input
                        id="contact-name"
                        type="text"
                        wire:model="name"
                        autocomplete="name"
                        placeholder="Your full name"
                        class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 text-ink dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-600 focus:outline-none focus:border-brand dark:focus:border-brand-400 transition-colors duration-150 @error('name') border-red-400 dark:border-red-500 @enderror"
                    >
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact-email" class="block text-xs font-semibold text-neutral-600 dark:text-neutral-400 uppercase tracking-wide mb-1.5">
                        Email Address <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <input
                        id="contact-email"
                        type="email"
                        wire:model="email"
                        autocomplete="email"
                        placeholder="you@example.com"
                        class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 text-ink dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-600 focus:outline-none focus:border-brand dark:focus:border-brand-400 transition-colors duration-150 @error('email') border-red-400 dark:border-red-500 @enderror"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Subject --}}
            <div class="mb-5">
                <label for="contact-subject" class="block text-xs font-semibold text-neutral-600 dark:text-neutral-400 uppercase tracking-wide mb-1.5">
                    Subject <span class="text-neutral-400 dark:text-neutral-600 font-normal normal-case tracking-normal">(optional)</span>
                </label>
                <input
                    id="contact-subject"
                    type="text"
                    wire:model="subject"
                    placeholder="What's this about?"
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 text-ink dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-600 focus:outline-none focus:border-brand dark:focus:border-brand-400 transition-colors duration-150"
                >
            </div>

            {{-- Message --}}
            <div class="mb-6">
                <label for="contact-message" class="block text-xs font-semibold text-neutral-600 dark:text-neutral-400 uppercase tracking-wide mb-1.5">
                    Message <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <textarea
                    id="contact-message"
                    wire:model="message"
                    rows="6"
                    placeholder="Tell us how we can help…"
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 text-ink dark:text-neutral-100 placeholder-neutral-400 dark:placeholder-neutral-600 focus:outline-none focus:border-brand dark:focus:border-brand-400 transition-colors duration-150 resize-none @error('message') border-red-400 dark:border-red-500 @enderror"
                ></textarea>
                @error('message')
                    <p class="mt-1.5 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3 bg-brand text-white text-sm font-semibold tracking-wide hover:bg-brand-600 active:bg-brand-700 transition-colors duration-150 disabled:opacity-60 disabled:cursor-not-allowed"
            >
                <span wire:loading.remove wire:target="submit">Send Message</span>
                <span wire:loading wire:target="submit" class="flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Sending…
                </span>
            </button>
        </form>
    @endif
</div>
