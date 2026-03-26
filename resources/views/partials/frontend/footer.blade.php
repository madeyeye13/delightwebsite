{{--
╔══════════════════════════════════════════════════════════════════╗
║  FRONTEND FOOTER PARTIAL                                          ║
║  resources/views/partials/frontend/footer.blade.php               ║
║                                                                   ║
║  Structure:                                                       ║
║  1. Main grid  — Brand | Quick Links | Help | Newsletter | Contact║
║  2. Divider                                                       ║
║  3. Payment icons row                                             ║
║  4. Bottom bar — Copyright | Legal links                          ║
╚══════════════════════════════════════════════════════════════════╝
--}}

<footer
    x-data="{
        currencyOpen: false,

        get selectedCurrency() {
            return Alpine.store('currency') ? Alpine.store('currency').active : 'NGN';
        },

        get currencyList() {
            var flagMap = { NGN:'🇳🇬', USD:'🇺🇸', GBP:'🇬🇧', EUR:'🇪🇺', CAD:'🇨🇦', GHS:'🇬🇭', ZAR:'🇿🇦', CFA:'🌍' };
            if (!Alpine.store('currency') || !Alpine.store('currency').rates) {
                return Object.entries(flagMap).map(function([code, flag]) { return { code: code, flag: flag }; });
            }
            return Object.keys(Alpine.store('currency').rates).map(function(code) {
                return { code: code, flag: flagMap[code] || '💱' };
            });
        },

        changeCurrency(code) {
            if (Alpine.store('currency')) {
                Alpine.store('currency').active = code;
            }
            this.currencyOpen = false;
            fetch('/currency/set', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code: code })
            });
        }
    }"
    @keydown.escape.window="currencyOpen = false"
    class="bg-black text-white"
    aria-label="Site footer"
>

    {{-- ── MAIN GRID ── --}}
    <div class="max-w-7xl mx-auto px-6 lg:px-10 pt-16 pb-10">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-5 lg:gap-10">

            {{-- ── COLUMN 1: Brand ── --}}
            <div class="sm:col-span-2 lg:col-span-1">
                <a href="{{ url('/') }}" class="inline-block mb-5" aria-label="1st Delightsome Fabrics — Home">
                    <img src="{{ asset('images/logowhite.png') }}" alt="1st Delightsome Fabrics" class="h-10 w-auto">
                </a>
                <p class="text-sm text-white/60 leading-relaxed mb-6 max-w-xs">
                    Your premier destination for authentic African lace, Aso-oke, and premium fabrics — crafted for royalty, delivered globally.
                </p>

                {{-- Social Icons --}}
                <div class="flex items-center gap-4">
                    <a href="#" aria-label="Instagram" class="text-white/50 hover:text-brand-400 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465.636.247 1.275.643 1.855 1.223.58.58.976 1.219 1.223 1.855C22 8.406 22 8.83 22 12c0 3.17 0 3.594-.06 4.608-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.223 1.855 4.902 4.902 0 01-1.855 1.223c-.636.247-1.363.416-2.427.465-1.024.047-1.379.06-3.808.06s-2.784-.013-3.808-.06c-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.855-1.223 4.902 4.902 0 01-1.223-1.855C2 18.594 2 18.17 2 15c0-3.17 0-3.594.06-4.608.049-1.064.218-1.791.465-2.427A4.902 4.902 0 013.748 6.11a4.902 4.902 0 011.855-1.223c.636-.247 1.363-.416 2.427-.465C9.054 4.375 9.408 4.362 12 4.362c2.43 0 2.785.013 3.808.06zm-.062-1.862C9.848 0 9.466.014 8.429.062c-1.068.049-1.798.22-2.437.469a6.764 6.764 0 00-2.445 1.591A6.764 6.764 0 002.06 4.567C1.811 5.206 1.64 5.936 1.591 7.004 1.542 8.042 1.528 8.424 1.528 12s.014 3.958.063 4.996c.049 1.068.22 1.798.469 2.437a6.764 6.764 0 001.591 2.445 6.764 6.764 0 002.445 1.591c.639.249 1.369.42 2.437.469C9.572 23.986 9.954 24 12.253 24s2.681-.014 3.719-.062c1.068-.049 1.798-.22 2.437-.469a6.764 6.764 0 002.445-1.591 6.764 6.764 0 001.591-2.445c.249-.639.42-1.369.469-2.437.048-1.038.062-1.42.062-4.996s-.014-3.958-.062-4.996c-.049-1.068-.22-1.798-.469-2.437a6.764 6.764 0 00-1.591-2.445A6.764 6.764 0 0018.409.531C17.77.282 17.04.111 15.972.062 14.934.014 14.552 0 12.253 0zm-.001 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="Facebook" class="text-white/50 hover:text-brand-400 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="TikTok" class="text-white/50 hover:text-brand-400 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.32 6.32 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.76a4.85 4.85 0 01-1.01-.07z"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="WhatsApp" class="text-white/50 hover:text-brand-400 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 018.413 3.488 11.824 11.824 0 013.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 01-5.688-1.448L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- ── COLUMN 2: Quick Links ── --}}
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-[1.5px] text-brand-400 mb-5">Quick Links</h3>
                <ul class="space-y-3" role="list">
                    <li>
                        <a href="{{ url('/') }}" class="text-sm text-white/70 hover:text-brand-400 transition-colors duration-200">Home</a>
                    </li>
                    <li>
                        <a href="{{ route('shop.index') }}" class="text-sm text-white/70 hover:text-brand-400 transition-colors duration-200">Shop</a>
                    </li>
                    <li>
                        <a href="{{ route('blog.index') }}" class="text-sm text-white/70 hover:text-brand-400 transition-colors duration-200">Blog</a>
                    </li>
                    <li>
                        <a href="{{ route('cart.index') }}" class="text-sm text-white/70 hover:text-brand-400 transition-colors duration-200">Cart</a>
                    </li>
                    <li>
                        <a href="{{ route('checkout.index') }}" class="text-sm text-white/70 hover:text-brand-400 transition-colors duration-200">Checkout</a>
                    </li>
                </ul>
            </div>

            {{-- ── COLUMN 3: Help & Support ── --}}
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-[1.5px] text-brand-400 mb-5">Help & Support</h3>
                <ul class="space-y-3" role="list">
                    <li>
                        <a href="#" class="text-sm text-white/70 hover:text-brand-400 transition-colors duration-200">FAQs</a>
                    </li>
                    <li>
                        <a href="#" class="text-sm text-white/70 hover:text-brand-400 transition-colors duration-200">Shipping Info</a>
                    </li>
                    <li>
                        <a href="#" class="text-sm text-white/70 hover:text-brand-400 transition-colors duration-200">Track My Order</a>
                    </li>
                    <li>
                        <a href="#" class="text-sm text-white/70 hover:text-brand-400 transition-colors duration-200">Size Guide</a>
                    </li>
                    <li>
                        <a href="#" class="text-sm text-white/70 hover:text-brand-400 transition-colors duration-200">Wholesale Inquiry</a>
                    </li>
                </ul>
            </div>

            {{-- ── COLUMN 4: Newsletter ── --}}
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-[1.5px] text-brand-400 mb-5">Newsletter</h3>
                <p class="text-sm text-white/60 leading-relaxed mb-5">
                    Get new arrivals, exclusive offers, and fabric inspiration straight to your inbox.
                </p>
                <div
                    x-data="{
                        email: '',
                        status: '',   {{-- '' | 'loading' | 'success' | 'error' --}}
                        message: '',
                        async submit() {
                            if (!this.email || !this.email.includes('@')) {
                                this.status = 'error';
                                this.message = 'Please enter a valid email address.';
                                return;
                            }
                            this.status = 'loading';
                            await new Promise(r => setTimeout(r, 800));
                            this.status = 'success';
                            this.message = 'You\'re subscribed — welcome!';
                            this.email = '';
                        }
                    }"
                >
                    <template x-if="status === 'success'">
                        <div class="flex items-center gap-2.5 text-sm text-emerald-400">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="message"></span>
                        </div>
                    </template>
                    <template x-if="status !== 'success'">
                        <div>
                            <div class="flex gap-0">
                                <label for="footer-newsletter-email" class="sr-only">Email address</label>
                                <input
                                    id="footer-newsletter-email"
                                    type="email"
                                    x-model="email"
                                    @keydown.enter="submit()"
                                    placeholder="Your email address"
                                    :disabled="status === 'loading'"
                                    class="flex-1 min-w-0 px-3.5 py-2.5 text-sm appearance-none bg-white/[0.08] border border-white/20 border-r-0 rounded-l text-white placeholder-white/35 focus:outline-none focus:border-white/40 focus:bg-white/[0.12] transition-colors duration-200 disabled:opacity-50"
                                    autocomplete="email"
                                >
                                <button
                                    @click="submit()"
                                    :disabled="status === 'loading'"
                                    class="shrink-0 px-4 py-2.5 bg-brand-500 text-white text-xs font-semibold tracking-wide rounded-r hover:bg-brand-400 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    aria-label="Subscribe to newsletter"
                                >
                                    <span x-show="status !== 'loading'">Subscribe</span>
                                    <span x-show="status === 'loading'" style="display:none">
                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                            <template x-if="status === 'error'">
                                <p x-text="message" class="mt-2 text-xs text-red-400"></p>
                            </template>
                            <p class="mt-3 text-xs text-white/30">No spam. Unsubscribe anytime.</p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ── COLUMN 5: Contact & Currency ── --}}
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-[1.5px] text-brand-400 mb-5">Get in Touch</h3>
                <ul class="space-y-3 mb-7" role="list">
                    <li>
                        <a href="mailto:hello@delightsome.com" class="flex items-center gap-2.5 text-sm text-white/70 hover:text-brand-400 transition-colors duration-200">
                            <svg class="w-4 h-4 shrink-0 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            hello@delightsome.com
                        </a>
                    </li>
                    <li>
                        <a href="tel:+2348000000000" class="flex items-center gap-2.5 text-sm text-white/70 hover:text-brand-400 transition-colors duration-200">
                            <svg class="w-4 h-4 shrink-0 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            +234 800 000 0000
                        </a>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 shrink-0 mt-0.5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-sm text-white/60 leading-relaxed">30b Opebi Rd, Opebi,<br>Ikeja Lagos 100281</span>
                    </li>
                </ul>

                {{-- ── CURRENCY SWITCHER ── --}}
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[1.5px] text-white/40 mb-3">Currency</p>
                    <div class="relative inline-block">
                        <button
                            @click="currencyOpen = !currencyOpen"
                            class="flex items-center gap-2 px-3.5 py-2 border border-white/20 hover:border-white/50 rounded text-sm text-white/80 hover:text-white transition-all duration-200 bg-white/5 hover:bg-white/10"
                            aria-haspopup="listbox"
                            :aria-expanded="currencyOpen"
                        >
                            <svg class="w-3.5 h-3.5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 004 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-text="selectedCurrency" class="font-medium tracking-wide"></span>
                            <svg class="w-3 h-3 text-white/50 transition-transform duration-200" :class="{ 'rotate-180': currencyOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- Dropdown --}}
                        <div
                            x-show="currencyOpen"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            @click.outside="currencyOpen = false"
                            class="absolute bottom-full left-0 mb-2 w-36 bg-neutral-900 border border-white/10 rounded shadow-2xl z-50 overflow-hidden"
                            role="listbox"
                            aria-label="Select currency"
                            style="display:none"
                        >
                            <template x-for="opt in currencyList" :key="opt.code">
                                <button
                                    @click="changeCurrency(opt.code)"
                                    :class="selectedCurrency === opt.code ? 'bg-white/10 text-white font-semibold' : 'text-white/65 hover:bg-white/5 hover:text-white'"
                                    class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-sm text-left transition-colors duration-150 border-none bg-transparent cursor-pointer"
                                    role="option"
                                    :aria-selected="selectedCurrency === opt.code"
                                >
                                    <span x-text="opt.flag" class="text-base leading-none"></span>
                                    <span x-text="opt.code" class="tracking-wide"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /grid --}}
    </div>

    {{-- ── DIVIDER ── --}}
    <div class="border-t border-white/10"></div>

    {{-- ── PAYMENT METHODS ── --}}
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-6">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-white/40 tracking-wide">Secure payments powered by industry-leading providers</p>
            <ul class="list list-payment flex items-center gap-2 flex-wrap justify-center sm:justify-end" role="list">
                <li class="list-payment__item">
                    <svg class="icon icon--full-color" viewBox="0 0 38 24" xmlns="http://www.w3.org/2000/svg" role="img" width="38" height="24" aria-labelledby="footer-pi-visa"><title id="footer-pi-visa">Visa</title><path opacity=".07" d="M35 0H3C1.3 0 0 1.3 0 3v18c0 1.7 1.4 3 3 3h32c1.7 0 3-1.3 3-3V3c0-1.7-1.4-3-3-3z"></path><path fill="#fff" d="M35 1c1.1 0 2 .9 2 2v18c0 1.1-.9 2-2 2H3c-1.1 0-2-.9-2-2V3c0-1.1.9-2 2-2h32"></path><path d="M28.3 10.1H28c-.4 1-.7 1.5-1 3h1.9c-.3-1.5-.3-2.2-.6-3zm2.9 5.9h-1.7c-.1 0-.1 0-.2-.1l-.2-.9-.1-.2h-2.4c-.1 0-.2 0-.2.2l-.3.9c0 .1-.1.1-.1.1h-2.1l.2-.5L27 8.7c0-.5.3-.7.8-.7h1.5c.1 0 .2 0 .2.2l1.4 6.5c.1.4.2.7.2 1.1.1.1.1.1.1.2zm-13.4-.3l.4-1.8c.1 0 .2.1.2.1.7.3 1.4.5 2.1.4.2 0 .5-.1.7-.2.5-.2.5-.7.1-1.1-.2-.2-.5-.3-.8-.5-.4-.2-.8-.4-1.1-.7-1.2-1-.8-2.4-.1-3.1.6-.4.9-.8 1.7-.8 1.2 0 2.5 0 3.1.2h.1c-.1.6-.2 1.1-.4 1.7-.5-.2-1-.4-1.5-.4-.3 0-.6 0-.9.1-.2 0-.3.1-.4.2-.2.2-.2.5 0 .7l.5.4c.4.2.8.4 1.1.6.5.3 1 .8 1.1 1.4.2.9-.1 1.7-.9 2.3-.5.4-.7.6-1.4.6-1.4 0-2.5.1-3.4-.2-.1.2-.1.2-.2.1zm-3.5.3c.1-.7.1-.7.2-1 .5-2.2 1-4.5 1.4-6.7.1-.2.1-.3.3-.3H18c-.2 1.2-.4 2.1-.7 3.2-.3 1.5-.6 3-1 4.5 0 .2-.1.2-.3.2M5 8.2c0-.1.2-.2.3-.2h3.4c.5 0 .9.3 1 .8l.9 4.4c0 .1 0 .1.1.2 0-.1.1-.1.1-.1l2.1-5.1c-.1-.1 0-.2.1-.2h2.1c0 .1 0 .1-.1.2l-3.1 7.3c-.1.2-.1.3-.2.4-.1.1-.3 0-.5 0H9.7c-.1 0-.2 0-.2-.2L7.9 9.5c-.2-.2-.5-.5-.9-.6-.6-.3-1.7-.5-1.9-.5L5 8.2z" fill="#142688"></path></svg>
                </li>
                <li class="list-payment__item">
                    <svg class="icon icon--full-color" viewBox="0 0 38 24" xmlns="http://www.w3.org/2000/svg" role="img" width="38" height="24" aria-labelledby="footer-pi-master"><title id="footer-pi-master">Mastercard</title><path opacity=".07" d="M35 0H3C1.3 0 0 1.3 0 3v18c0 1.7 1.4 3 3 3h32c1.7 0 3-1.3 3-3V3c0-1.7-1.4-3-3-3z"></path><path fill="#fff" d="M35 1c1.1 0 2 .9 2 2v18c0 1.1-.9 2-2 2H3c-1.1 0-2-.9-2-2V3c0-1.1.9-2 2-2h32"></path><circle fill="#EB001B" cx="15" cy="12" r="7"></circle><circle fill="#F79E1B" cx="23" cy="12" r="7"></circle><path fill="#FF5F00" d="M22 12c0-2.4-1.2-4.5-3-5.7-1.8 1.3-3 3.4-3 5.7s1.2 4.5 3 5.7c1.8-1.2 3-3.3 3-5.7z"></path></svg>
                </li>
                <li class="list-payment__item">
                    <svg class="icon icon--full-color" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="footer-pi-amex" viewBox="0 0 38 24" width="38" height="24"><title id="footer-pi-amex">American Express</title><path fill="#000" d="M35 0H3C1.3 0 0 1.3 0 3v18c0 1.7 1.4 3 3 3h32c1.7 0 3-1.3 3-3V3c0-1.7-1.4-3-3-3Z" opacity=".07"></path><path fill="#006FCF" d="M35 1c1.1 0 2 .9 2 2v18c0 1.1-.9 2-2 2H3c-1.1 0-2-.9-2-2V3c0-1.1.9-2 2-2h32Z"></path><path fill="#FFF" d="M22.012 19.936v-8.421L37 11.528v2.326l-1.732 1.852L37 17.573v2.375h-2.766l-1.47-1.622-1.46 1.628-9.292-.02Z"></path><path fill="#006FCF" d="M23.013 19.012v-6.57h5.572v1.513h-3.768v1.028h3.678v1.488h-3.678v1.01h3.768v1.531h-5.572Z"></path><path fill="#006FCF" d="m28.557 19.012 3.083-3.289-3.083-3.282h2.386l1.884 2.083 1.89-2.082H37v.051l-3.017 3.23L37 18.92v.093h-2.307l-1.917-2.103-1.898 2.104h-2.321Z"></path><path fill="#FFF" d="M22.71 4.04h3.614l1.269 2.881V4.04h4.46l.77 2.159.771-2.159H37v8.421H19l3.71-8.421Z"></path><path fill="#006FCF" d="m23.395 4.955-2.916 6.566h2l.55-1.315h2.98l.55 1.315h2.05l-2.904-6.566h-2.31Zm.25 3.777.875-2.09.873 2.09h-1.748Z"></path><path fill="#006FCF" d="M28.581 11.52V4.953l2.811.01L32.84 9l1.456-4.046H37v6.565l-1.74.016v-4.51l-1.644 4.494h-1.59L30.35 7.01v4.51h-1.768Z"></path></svg>
                </li>
                <li class="list-payment__item">
                    <svg class="icon icon--full-color" viewBox="0 0 38 24" xmlns="http://www.w3.org/2000/svg" width="38" height="24" role="img" aria-labelledby="footer-pi-paypal"><title id="footer-pi-paypal">PayPal</title><path opacity=".07" d="M35 0H3C1.3 0 0 1.3 0 3v18c0 1.7 1.4 3 3 3h32c1.7 0 3-1.3 3-3V3c0-1.7-1.4-3-3-3z"></path><path fill="#fff" d="M35 1c1.1 0 2 .9 2 2v18c0 1.1-.9 2-2 2H3c-1.1 0-2-.9-2-2V3c0-1.1.9-2 2-2h32"></path><path fill="#003087" d="M23.9 8.3c.2-1 0-1.7-.6-2.3-.6-.7-1.7-1-3.1-1h-4.1c-.3 0-.5.2-.6.5L14 15.6c0 .2.1.4.3.4H17l.4-3.4 1.8-2.2 4.7-2.1z"></path><path fill="#3086C8" d="M23.9 8.3l-.2.2c-.5 2.8-2.2 3.8-4.6 3.8H18c-.3 0-.5.2-.6.5l-.6 3.9-.2 1c0 .2.1.4.3.4H19c.3 0 .5-.2.5-.4v-.1l.4-2.4v-.1c0-.2.3-.4.5-.4h.3c2.1 0 3.7-.8 4.1-3.2.2-1 .1-1.8-.4-2.4-.1-.5-.3-.7-.5-.8z"></path><path fill="#012169" d="M23.3 8.1c-.1-.1-.2-.1-.3-.1-.1 0-.2 0-.3-.1-.3-.1-.7-.1-1.1-.1h-3c-.1 0-.2 0-.2.1-.2.1-.3.2-.3.4l-.7 4.4v.1c0-.3.3-.5.6-.5h1.3c2.5 0 4.1-1 4.6-3.8v-.2c-.1-.1-.3-.2-.5-.2h-.1z"></path></svg>
                </li>
                <li class="list-payment__item">
                    <svg class="icon icon--full-color" viewBox="0 0 38 24" xmlns="http://www.w3.org/2000/svg" role="img" width="38" height="24" aria-labelledby="footer-pi-diners"><title id="footer-pi-diners">Diners Club</title><path opacity=".07" d="M35 0H3C1.3 0 0 1.3 0 3v18c0 1.7 1.4 3 3 3h32c1.7 0 3-1.3 3-3V3c0-1.7-1.4-3-3-3z"></path><path fill="#fff" d="M35 1c1.1 0 2 .9 2 2v18c0 1.1-.9 2-2 2H3c-1.1 0-2-.9-2-2V3c0-1.1.9-2 2-2h32"></path><path d="M12 12v3.7c0 .3-.2.3-.5.2-1.9-.8-3-3.3-2.3-5.4.4-1.1 1.2-2 2.3-2.4.4-.2.5-.1.5.2V12zm2 0V8.3c0-.3 0-.3.3-.2 2.1.8 3.2 3.3 2.4 5.4-.4 1.1-1.2 2-2.3 2.4-.4.2-.4.1-.4-.2V12zm7.2-7H13c3.8 0 6.8 3.1 6.8 7s-3 7-6.8 7h8.2c3.8 0 6.8-3.1 6.8-7s-3-7-6.8-7z" fill="#3086C8"></path></svg>
                </li>
                <li class="list-payment__item">
                    <svg class="icon icon--full-color" viewBox="0 0 38 24" width="38" height="24" role="img" aria-labelledby="footer-pi-discover" fill="none" xmlns="http://www.w3.org/2000/svg"><title id="footer-pi-discover">Discover</title><path fill="#000" opacity=".07" d="M35 0H3C1.3 0 0 1.3 0 3v18c0 1.7 1.4 3 3 3h32c1.7 0 3-1.3 3-3V3c0-1.7-1.4-3-3-3z"></path><path d="M35 1c1.1 0 2 .9 2 2v18c0 1.1-.9 2-2 2H3c-1.1 0-2-.9-2-2V3c0-1.1.9-2 2-2h32z" fill="#fff"></path><path d="M3.57 7.16H2v5.5h1.57c.83 0 1.43-.2 1.96-.63.63-.52 1-1.3 1-2.11-.01-1.63-1.22-2.76-2.96-2.76zm1.26 4.14c-.34.3-.77.44-1.47.44h-.29V8.1h.29c.69 0 1.11.12 1.47.44.37.33.59.84.59 1.37 0 .53-.22 1.06-.59 1.39zm2.19-4.14h1.07v5.5H7.02v-5.5zm3.69 2.11c-.64-.24-.83-.4-.83-.69 0-.35.34-.61.8-.61.32 0 .59.13.86.45l.56-.73c-.46-.4-1.01-.61-1.62-.61-.97 0-1.72.68-1.72 1.58 0 .76.35 1.15 1.35 1.51.42.15.63.25.74.31.21.14.32.34.32.57 0 .45-.35.78-.83.78-.51 0-.92-.26-1.17-.73l-.69.67c.49.73 1.09 1.05 1.9 1.05 1.11 0 1.9-.74 1.9-1.81.02-.89-.35-1.29-1.57-1.74zm1.92.65c0 1.62 1.27 2.87 2.9 2.87.46 0 .86-.09 1.34-.32v-1.26c-.43.43-.81.6-1.29.6-1.08 0-1.85-.78-1.85-1.9 0-1.06.79-1.89 1.8-1.89.51 0 .9.18 1.34.62V7.38c-.47-.24-.86-.34-1.32-.34-1.61 0-2.92 1.28-2.92 2.88zm12.76.94l-1.47-3.7h-1.17l2.33 5.64h.58l2.37-5.64h-1.16l-1.48 3.7zm3.13 1.8h3.04v-.93h-1.97v-1.48h1.9v-.93h-1.9V8.1h1.97v-.94h-3.04v5.5zm7.29-3.87c0-1.03-.71-1.62-1.95-1.62h-1.59v5.5h1.07v-2.21h.14l1.48 2.21h1.32l-1.73-2.32c.81-.17 1.26-.72 1.26-1.56zm-2.16.91h-.31V8.03h.33c.67 0 1.03.28 1.03.82 0 .55-.36.85-1.05.85z" fill="#231F20"></path><path d="M20.16 12.86a2.931 2.931 0 100-5.862 2.931 2.931 0 000 5.862z" fill="url(#footer-pi-paint0_linear)"></path><path opacity=".65" d="M20.16 12.86a2.931 2.931 0 100-5.862 2.931 2.931 0 000 5.862z" fill="url(#footer-pi-paint1_linear)"></path><path d="M36.57 7.506c0-.1-.07-.15-.18-.15h-.16v.48h.12v-.19l.14.19h.14l-.16-.2c.06-.01.1-.06.1-.13zm-.2.07h-.02v-.13h.02c.06 0 .09.02.09.06 0 .05-.03.07-.09.07z" fill="#231F20"></path><path d="M36.41 7.176c-.23 0-.42.19-.42.42 0 .23.19.42.42.42.23 0 .42-.19.42-.42 0-.23-.19-.42-.42-.42zm0 .77c-.18 0-.34-.15-.34-.35 0-.19.15-.35.34-.35.18 0 .33.16.33.35 0 .19-.15.35-.33.35z" fill="#231F20"></path><path d="M37 12.984S27.09 19.873 8.976 23h26.023a2 2 0 002-1.984l.024-3.02L37 12.985z" fill="#F48120"></path><defs><linearGradient id="footer-pi-paint0_linear" x1="21.657" y1="12.275" x2="19.632" y2="9.104" gradientUnits="userSpaceOnUse"><stop stop-color="#F89F20"></stop><stop offset=".25" stop-color="#F79A20"></stop><stop offset=".533" stop-color="#F68D20"></stop><stop offset=".62" stop-color="#F58720"></stop><stop offset=".723" stop-color="#F48120"></stop><stop offset="1" stop-color="#F37521"></stop></linearGradient><linearGradient id="footer-pi-paint1_linear" x1="21.338" y1="12.232" x2="18.378" y2="6.446" gradientUnits="userSpaceOnUse"><stop stop-color="#F58720"></stop><stop offset=".359" stop-color="#E16F27"></stop><stop offset=".703" stop-color="#D4602C"></stop><stop offset=".982" stop-color="#D05B2E"></stop></linearGradient></defs></svg>
                </li>
            </ul>
        </div>
    </div>

    {{-- ── DIVIDER ── --}}
    <div class="border-t border-white/10"></div>

    {{-- ── BOTTOM BAR ── --}}
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-5">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            {{-- Copyright --}}
            <p class="text-xs text-white/40 order-2 sm:order-1">
                &copy; {{ date('Y') }} 1st Delightsome Fabrics. All rights reserved.
            </p>

            {{-- Legal links --}}
            <nav class="flex items-center gap-5 order-1 sm:order-2" aria-label="Legal">
                <a href="#" class="text-xs text-white/50 hover:text-brand-400 transition-colors duration-200">Privacy Policy</a>
                <span class="text-white/20 text-xs" aria-hidden="true">·</span>
                <a href="#" class="text-xs text-white/50 hover:text-brand-400 transition-colors duration-200">Return Policy</a>
                <span class="text-white/20 text-xs" aria-hidden="true">·</span>
                <a href="#" class="text-xs text-white/50 hover:text-brand-400 transition-colors duration-200">Terms &amp; Conditions</a>
            </nav>
        </div>
    </div>

</footer>
