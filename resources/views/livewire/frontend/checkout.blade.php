{{-- resources/views/livewire/frontend/checkout.blade.php --}}
{{-- Livewire wrapper — Alpine data is defined inline in script below so it   --}}
{{-- survives Livewire re-renders. The component is a thin shell; all UI state  --}}
{{-- lives in Alpine. $wire calls reach the Checkout Livewire class for heavy   --}}
{{-- backend work (shipping calc, order creation, etc.).                        --}}
<div>

<style>
    [x-cloak] { display: none !important; }

    /* ─── Fields — light + dark ──────────────────────────────────── */
    .co-field {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #D4D4D4;
        background: #fff;
        font-size: 13px;
        color: #171717;
        outline: none;
        transition: border-color 0.15s, background 0.15s, color 0.15s;
        border-radius: 0;
        font-family: 'Plus Jakarta Sans', sans-serif;
        display: block;
        box-sizing: border-box;
    }
    .co-field:focus        { border-color: #1F6F67; box-shadow: none !important; --tw-ring-shadow: none !important; }
    .co-field::placeholder { color: #A3A3A3; }
    .co-field-muted        { background: #F9F9F9; }
    select.co-field {
        cursor: pointer;
        -webkit-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23737373' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 14px;
        padding-right: 30px;
    }
    .dark .co-field {
        border-color: #404040;
        background: #262626;
        color: #F9F9F9;
    }
    .dark .co-field::placeholder { color: #525252; }
    .dark .co-field-muted        { background: #1a1a1a; }
    .dark select.co-field {
        -webkit-appearance: none !important;
        appearance:         none !important;
        background-color:   #262626 !important;
        background-image:   url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23737373' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") !important;
        background-repeat:   no-repeat !important;
        background-position: right 10px center !important;
        background-size:     14px !important;
    }
    .dark .co-field option { background: #262626; color: #F9F9F9; }

    /* ─── Step panel ─────────────────────────────────────────────── */
    .step-panel { animation: panelIn 0.2s ease; }
    @keyframes panelIn {
        from { opacity: 0; transform: translateY(5px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ─── Stepper connector ──────────────────────────────────────── */
    .co-connector {
        flex: 1; height: 1px; background: #E5E5E5;
        margin: 0 6px; position: relative; top: -14px;
        transition: background 0.3s;
    }
    .co-connector.done { background: #1F6F67; }
    .dark .co-connector { background: #404040; }
    .dark .co-connector.done { background: #1F6F67; }

    /* ─── Shipping radio card ────────────────────────────────────── */
    .ship-card {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 12px; border: 1px solid #E5E5E5;
        cursor: pointer; transition: border-color 0.15s, background 0.15s;
    }
    .ship-card:hover    { border-color: #A3A3A3; }
    .ship-card.selected { border-color: #1F6F67; background: #E6F3F2; }
    .dark .ship-card              { border-color: #404040; }
    .dark .ship-card:hover        { border-color: #525252; }
    .dark .ship-card.selected     { border-color: #1F6F67; background: #0D3230; }

    /* ─── Payment card ───────────────────────────────────────────── */
    .pay-card {
        position: relative; padding: 14px;
        border: 1px solid #E5E5E5; cursor: pointer;
        transition: border-color 0.15s, background 0.15s;
    }
    .pay-card:hover    { border-color: #A3A3A3; }
    .pay-card.selected { border-color: #1F6F67; background: #E6F3F2; }
    .dark .pay-card            { border-color: #404040; }
    .dark .pay-card:hover      { border-color: #525252; }
    .dark .pay-card.selected   { border-color: #1F6F67; background: #0D3230; }

    /* ─── Address autocomplete ───────────────────────────────────── */
    .addr-drop {
        position: absolute; top: 100%; left: 0; right: 0;
        background: #fff; border: 1px solid #D4D4D4;
        border-top: none; z-index: 30;
        max-height: 220px; overflow-y: auto;
    }
    .addr-item {
        display: flex; align-items: flex-start; gap: 8px;
        padding: 9px 12px; border-bottom: 1px solid #F3F3F3;
        cursor: pointer; transition: background 0.1s;
    }
    .addr-item:last-child { border-bottom: none; }
    .addr-item:hover      { background: #F9F9F9; }
    .dark .addr-drop      { background: #262626; border-color: #404040; }
    .dark .addr-item      { border-color: #404040; }
    .dark .addr-item:hover{ background: #404040; }

    /* ─── Qty stepper ────────────────────────────────────────────── */
    .qty-btn {
        width: 26px; height: 26px; border: 1px solid #D4D4D4;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; color: #525252; cursor: pointer;
        transition: background 0.1s; line-height: 1;
    }
    .qty-btn:hover { background: #F3F3F3; }
    .qty-display {
        width: 34px; height: 26px;
        border-top: 1px solid #D4D4D4; border-bottom: 1px solid #D4D4D4;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 600; color: #171717;
    }
    .dark .qty-btn         { border-color: #404040; color: #A3A3A3; }
    .dark .qty-btn:hover   { background: #404040; }
    .dark .qty-display     { border-color: #404040; color: #F9F9F9; }

    /* ─── Success icon ───────────────────────────────────────────── */
    @keyframes successPulse {
        0%   { transform: scale(0.8); opacity: 0; }
        70%  { transform: scale(1.05); }
        100% { transform: scale(1); opacity: 1; }
    }
    .success-icon { animation: successPulse 0.4s ease forwards; }

    /* ─── Scrollbar ──────────────────────────────────────────────── */
    ::-webkit-scrollbar       { width: 4px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #D4D4D4; }
    .dark ::-webkit-scrollbar-thumb { background: #404040; }

    input[type="radio"],
    input[type="checkbox"] { accent-color: #1F6F67; }

    /* ─── Notice banners ─────────────────────────────────────────── */
    .dark .co-notice-brand { background: #0D3230 !important; border-color: #134643 !important; }
    .dark .co-notice-accent { background: #3B2B07 !important; border-color: #65490C !important; }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('checkoutApp', () => ({

            currentStep: 1,
            summaryOpen: true,
            isGuest: {{ $isGuest ? 'true' : 'false' }},
            showPassword: false,
            orderNumber: '',
            placingOrder: false,
            orderError: '',
            shippingLoading: false,
            formErrors: {},

            promoCode:     '',
            promoApplied:  false,
            promoDiscount: 0,
            promoDiscountAmount: 0,
            promoError:    '',

            addressSuggestions: [],
            showSuggestions:    false,
            _addrTimer:         null,

            init() {
                this.summaryOpen = window.innerWidth >= 1024;
                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 1024) this.summaryOpen = true;
                });

                // Populate cart from Alpine store on init
                this.$nextTick(() => {
                    if (Alpine.store('cart') && Alpine.store('cart').items.length > 0) {
                        this.syncCartFromStore();
                    }
                    window.addEventListener('cart:synced', () => this.syncCartFromStore());
                    window.addEventListener('cart:initialized', () => this.syncCartFromStore());
                });

                // When country changes, syncCurrency() dispatches this event from Livewire.
                // Update the shared currency store so the header and all prices stay in sync.
                window.addEventListener('currency:changed', (e) => {
                    const newCurrency = e.detail?.currency || (typeof e.detail === 'string' ? e.detail : null);
                    if (Alpine.store('currency') && newCurrency) {
                        Alpine.store('currency').active = newCurrency;
                    }
                    // Re-fetch shipping in the new currency
                    if (this.shippingMethods.length > 0 || this.form.address.country) {
                        this.fetchShippingOptions();
                    }
                });
            },

            syncCartFromStore() {
                const storeItems = Alpine.store('cart').items || [];
                this.cart.items = storeItems.map(item => ({
                    id: item.product_id,
                    cart_line_id: item.cart_line_id,
                    name: item.name,
                    price: item.unit_price,
                    qty: item.quantity,
                    configType: (item.selling_method || 'per_piece').replace(/-/g, '_'),
                    unitLabel: item.unit_label || 'piece',
                    unitsPerOrder: item.units_per_order || 1,
                    lengthUnit: item.length_unit || 'yards',
                    loomSize: item.loom_size || '',
                    minQuantity: item.min_quantity || 1,
                    quantityStep: item.quantity_step || 1,
                    stockQuantity: item.stock_quantity || 99,
                    weight: item.weight_kg || 1.5,
                    variant: item.selected_variant ? {
                        id: item.selected_variant.id,
                        name: item.selected_variant.color || item.selected_variant.name,
                        hex: item.selected_variant.hex || '#cccccc',
                    } : null,
                    image: item.image || '',
                }));

                // Add-ons: items with added_add_ons
                this.cart.addOns = [];
                storeItems.forEach(item => {
                    (item.added_add_ons || []).forEach(ao => {
                        this.cart.addOns.push({
                            id: ao.product_id,
                            name: ao.name,
                            price: ao.unit_price,
                            qty: ao.quantity || 1,
                            configType: (ao.selling_method || 'per_piece').replace(/-/g, '_'),
                            unitLabel: ao.unit_label || 'piece',
                            unitsPerOrder: ao.units_per_order || 1,
                            lengthUnit: ao.length_unit || 'yards',
                            minQuantity: ao.min_quantity || 1,
                            quantityStep: ao.quantity_step || 1,
                            variant: null,
                        });
                    });
                });
            },

            form: {
                contact: {
                    fullName: @js($authUser?->name ?? ''), email: @js($authUser?->email ?? ''), phoneCode: '+234', phone: @js($authUser?->phone ?? ''),
                    password: '', confirmPassword: '',
                },
                address: {
                    street: '', houseNo: '', city: '', state: '',
                    country: 'NG', postal: '', notes: '',
                },
                shippingMethod: null,
                paymentMethod:  '',
            },

            countries: [
                { code: 'NG', name: 'Nigeria',       flag: '🇳🇬' },
                { code: 'US', name: 'United States',  flag: '🇺🇸' },
                { code: 'GB', name: 'United Kingdom', flag: '🇬🇧' },
                { code: 'GH', name: 'Ghana',          flag: '🇬🇭' },
                { code: 'CA', name: 'Canada',          flag: '🇨🇦' },
                { code: 'CM', name: 'Cameroon',        flag: '🇨🇲' },
                { code: 'ZA', name: 'South Africa',    flag: '🇿🇦' },
                { code: 'AU', name: 'Australia',       flag: '🇦🇺' },
                { code: 'DE', name: 'Germany',         flag: '🇩🇪' },
                { code: 'FR', name: 'France',          flag: '🇫🇷' },
            ],

            nigeriaStates: [
                'Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno',
                'Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT - Abuja','Gombe',
                'Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos',
                'Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto',
                'Taraba','Yobe','Zamfara',
            ],

            shippingMethods: [],

            get selectedShipping() {
                if (!this.form.shippingMethod) return null;
                return this.shippingMethods.find(m => m.id === this.form.shippingMethod) || null;
            },

            cart: {
                items: [],
                addOns: [],
            },

            // ─── ADDRESS AUTOCOMPLETE ─────────────────────────────────────────
            addrLoading: false,

            searchAddress(query) {
                clearTimeout(this._addrTimer);
                this.addressSuggestions = [];

                if (query.length < 4) {
                    this.showSuggestions = false;
                    return;
                }

                this._addrTimer = setTimeout(async () => {
                    this.addrLoading = true;
                    try {
                        const cc  = (this.form.address.country || 'NG').toLowerCase();
                        const url = new URL('https://nominatim.openstreetmap.org/search');
                        url.searchParams.set('q',              query);
                        url.searchParams.set('format',         'json');
                        url.searchParams.set('addressdetails', '1');
                        url.searchParams.set('limit',          '6');
                        url.searchParams.set('countrycodes',   cc);

                        const res  = await fetch(url.toString(), {
                            headers: {
                                'Accept-Language': 'en',
                                'User-Agent':      '1stDelightsomeFabrics/1.0 (checkout)',
                            },
                        });

                        if (!res.ok) throw new Error('Nominatim error');
                        const data = await res.json();

                        this.addressSuggestions = data.map(place => {
                            const a    = place.address || {};
                            const road = [a.house_number, a.road].filter(Boolean).join(' ');
                            const ctx  = [
                                a.suburb || a.neighbourhood || a.quarter,
                                a.city   || a.town || a.village || a.county,
                                a.state,
                            ].filter(Boolean).join(', ');
                            const stateName = a.state ? a.state.replace(' State', '') : '';
                            return {
                                main:      road || place.display_name.split(',')[0],
                                secondary: ctx  || place.display_name,
                                city:      a.city || a.town || a.village || a.county || '',
                                state:     this._matchNigeriaState(stateName),
                                country:   this.form.address.country || 'NG',
                                postal:    a.postcode || '',
                            };
                        }).filter(s => s.main);

                        this.showSuggestions = this.addressSuggestions.length > 0;
                    } catch (e) {
                        this.addressSuggestions = [];
                        this.showSuggestions    = false;
                    } finally {
                        this.addrLoading = false;
                    }
                }, 800);
            },

            _matchNigeriaState(nominatimState) {
                if (!nominatimState) return '';
                const clean = nominatimState.toLowerCase().replace(' state', '').trim();
                const match = this.nigeriaStates.find(s =>
                    s.toLowerCase().replace('fct - abuja', 'abuja').includes(clean) ||
                    clean.includes(s.toLowerCase().replace('fct - abuja', 'abuja'))
                );
                return match || nominatimState;
            },

            selectAddress(s) {
                this.form.address.street  = s.main;
                this.form.address.city    = s.city;
                this.form.address.state   = s.state;
                this.form.address.country = s.country;
                this.form.address.postal  = s.postal || '';
                this.showSuggestions      = false;
                this.addressSuggestions   = [];
                // Trigger shipping recalc after address selection
                this.fetchShippingOptions();
            },

            async onCountryChange() {
                this.form.address.state  = '';
                this.form.address.city   = '';
                this.form.address.postal = '';
                this.shippingMethods     = [];
                this.form.shippingMethod = null;
                // Sync currency in header (fires currency:changed event which updates Alpine store)
                await this.$wire.syncCurrency(this.form.address.country);
                // Fetch shipping immediately (e.g. store pickup is always available for NG)
                await this.fetchShippingOptions();
            },

            async fetchShippingOptions() {
                const country = this.form.address.country;
                const state   = this.form.address.state;
                const city    = this.form.address.city;
                if (!country) return;

                this.shippingLoading = true;
                this.shippingMethods = [];
                this.form.shippingMethod = null;

                try {
                    const totalWeight = this.cart.items.reduce((s, i) => s + ((i.weight || 1.5) * i.qty), 0);
                    const postal      = this.form.address.postal || '';
                    // Always fetch prices in NGN so fmt() can convert to active currency
                    const currency    = 'NGN';

                    const methods = await this.$wire.calculateShipping(country, state, city, postal, totalWeight, currency);
                    this.shippingMethods = methods && methods.length > 0 ? methods : [];

                    // Auto-select first non-contact-required option
                    const defaultMethod = this.shippingMethods.find(m => !m.contact_required);
                    if (defaultMethod) {
                        this.form.shippingMethod = defaultMethod.id;
                    }
                } catch(e) {
                    console.error('Shipping calc failed', e);
                } finally {
                    this.shippingLoading = false;
                }
            },

            goTo(step) {
                this.currentStep = step;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            async goToPayment() {
                this.formErrors = {};
                const c = this.form.contact;
                const a = this.form.address;

                if (!c.fullName.trim())  this.formErrors.fullName = 'Full name is required';
                if (!c.email.trim())     this.formErrors.email    = 'Email address is required';
                if (!c.phone.trim())     this.formErrors.phone    = 'Phone number is required';
                if (!a.country)          this.formErrors.country  = 'Please select a country';
                if (!a.street.trim())    this.formErrors.street   = 'Street address is required';
                if (!a.city.trim())      this.formErrors.city     = 'City is required';

                if (Object.keys(this.formErrors).length > 0) {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }

                if (this.shippingMethods.length === 0) {
                    await this.fetchShippingOptions();
                }

                if (!this.form.shippingMethod && this.shippingMethods.length > 0) {
                    this.formErrors.shippingMethod = 'Please select a shipping method';
                    return;
                }

                this.goTo(2);
            },

            async placeOrder() {
                if (!this.form.paymentMethod || this.placingOrder) return;

                this.orderError  = '';
                this.placingOrder = true;

                try {
                    const result = await this.$wire.placeOrder({
                        contact:        this.form.contact,
                        address:        this.form.address,
                        shippingMethod: this.selectedShipping || { id: this.form.shippingMethod, price: this.shippingCost(), estimated_days: null },
                        paymentMethod:  this.form.paymentMethod,
                        promoCode:      this.promoApplied ? this.promoCode : null,
                    });

                    if (result && result.success) {
                        this.orderNumber = result.order_number;
                        if (result.payment_url) {
                            window.location.href = result.payment_url;
                        } else {
                            this.goTo(3);
                        }
                    } else {
                        this.orderError = (result && result.error) ? result.error : 'Something went wrong. Please try again.';
                    }
                } catch(e) {
                    this.orderError = 'Connection error. Please try again.';
                    console.error('placeOrder error', e);
                } finally {
                    this.placingOrder = false;
                }
            },

            incQty(item) { item.qty = Math.min(item.qty + (item.quantityStep || 1), item.stockQuantity || 999); },
            decQty(item) {
                const min = item.minQuantity || 1, step = item.quantityStep || 1;
                if (item.qty - step >= min) item.qty -= step;
            },
            removeItem(item)   { this.cart.items  = this.cart.items.filter(i => i.id !== item.id); },
            removeAddon(addon) { this.cart.addOns = this.cart.addOns.filter(a => a.id !== addon.id); },

            // ─── UNIT LABEL ───────────────────────────────────────────────────
            unitLabel(item) {
                const cfg = item.configType, qty = item.qty, step = item.unitsPerOrder || 1;
                if (cfg === 'per_length') {
                    return `${qty} unit${qty !== 1 ? 's' : ''} × ${step} ${item.lengthUnit} = ${qty * step} ${item.lengthUnit} total`;
                }
                if (cfg === 'per_loom') {
                    return `${qty} loom${qty !== 1 ? 's' : ''}${item.loomSize ? ' (' + item.loomSize + ')' : ''}`;
                }
                if (cfg === 'per_set')    return `${qty} set${qty !== 1 ? 's' : ''}`;
                if (cfg === 'per_bundle') return `${qty} bundle${qty !== 1 ? 's' : ''}`;
                const lbl = item.unitLabel || 'piece';
                return `${qty} ${lbl}${qty !== 1 ? 's' : ''}`;
            },

            async applyPromo() {
                const code = (this.promoCode || '').trim().toUpperCase();
                if (!code) { this.promoError = 'Please enter a code'; return; }
                try {
                    const result = await this.$wire.validatePromo(code, this.subtotal());
                    if (result.valid) {
                        this.promoDiscount       = result.discount_percent || 0;
                        this.promoDiscountAmount = result.discount_amount || 0;
                        this.promoApplied        = true;
                        this.promoError          = '';
                    } else {
                        this.promoError    = result.message || 'Invalid code';
                        this.promoApplied  = false;
                    }
                } catch(e) {
                    this.promoError = 'Could not validate code. Please try again.';
                }
            },
            clearPromo() { this.promoApplied = false; this.promoDiscount = 0; this.promoDiscountAmount = 0; this.promoCode = ''; this.promoError = ''; },

            subtotal()     { return this.cart.items.reduce((s, i) => s + i.price * i.qty, 0); },
            addOnsTotal()  { return (this.cart.addOns || []).reduce((s, a) => s + a.price * (a.qty || 1), 0); },
            promoSavings() { return this.promoApplied ? Math.round(this.subtotal() * this.promoDiscount / 100) : 0; },
            shippingCost() {
                const m = this.selectedShipping;
                return m ? (m.price || 0) : 0;
            },
            getTotal()     { return this.subtotal() + this.addOnsTotal() - this.promoSavings() + this.shippingCost(); },

            fmt(n) {
                const store = Alpine.store('currency');
                if (store && typeof store.format === 'function') { return store.format(n); }
                return '₦' + Math.round(Number(n)).toLocaleString();
            },

        })); // Alpine.data
    }); // alpine:init
</script>

{{-- x-data="checkoutApp" — no () when using Alpine.data() --}}
<div x-data="checkoutApp" class="bg-neutral-50 dark:bg-neutral-900 min-h-screen pt-40">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 pb-16">

        {{-- ═══ STEPPER ════════════════════════════════════════════════════ --}}
        <div class="flex items-start justify-center mb-7 select-none">

            <div class="flex flex-col items-center gap-1">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold border transition-all"
                     :class="currentStep >= 1 ? 'bg-brand border-brand text-white' : 'bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 text-neutral-400'">
                    <template x-if="currentStep > 1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="currentStep <= 1"><span>1</span></template>
                </div>
                <span class="text-[10px] font-semibold tracking-wide"
                      :class="currentStep === 1 ? 'text-brand' : currentStep > 1 ? 'text-neutral-500' : 'text-neutral-400 dark:text-neutral-600'">SHIPPING</span>
            </div>

            <div class="co-connector" :class="currentStep > 1 ? 'done' : ''"></div>

            <div class="flex flex-col items-center gap-1">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold border transition-all"
                     :class="currentStep >= 2 ? 'bg-brand border-brand text-white' : 'bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 text-neutral-400'">
                    <template x-if="currentStep > 2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="currentStep <= 2"><span>2</span></template>
                </div>
                <span class="text-[10px] font-semibold tracking-wide"
                      :class="currentStep === 2 ? 'text-brand' : currentStep > 2 ? 'text-neutral-500' : 'text-neutral-400 dark:text-neutral-600'">PAYMENT</span>
            </div>

            <div class="co-connector" :class="currentStep > 2 ? 'done' : ''"></div>

            <div class="flex flex-col items-center gap-1">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold border transition-all"
                     :class="currentStep === 3 ? 'bg-brand border-brand text-white' : 'bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 text-neutral-400'">
                    <template x-if="currentStep === 3">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="currentStep !== 3"><span>3</span></template>
                </div>
                <span class="text-[10px] font-semibold tracking-wide"
                      :class="currentStep === 3 ? 'text-brand' : 'text-neutral-400 dark:text-neutral-600'">FINISH</span>
            </div>

        </div>

        {{-- ═══ TWO-COLUMN GRID ═════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

            {{-- ════════════════════════════════════════════════════════════
                 LEFT — FORM STEPS
            ════════════════════════════════════════════════════════════════ --}}
            <div class="lg:col-span-7 space-y-3">

                {{-- ═══ STEP 1 · SHIPPING ═════════════════════════════════ --}}
                <template x-if="currentStep === 1">
                <div class="space-y-3 step-panel">

                    {{-- Contact Information --}}
                    <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700">
                        <div class="px-5 py-3 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between">
                            <h2 class="font-display font-semibold text-neutral-900 dark:text-neutral-50 text-sm">Contact Information</h2>
                            @guest
                            <span class="text-xs text-neutral-400 dark:text-neutral-500">
                                Already have an account?
                                <a href="{{ route('login') }}" class="text-brand dark:text-brand-300 font-medium hover:text-brand-600 dark:hover:text-brand-200 transition-colors">Sign in</a>
                            </span>
                            @endguest
                        </div>
                        <div class="px-5 py-4 space-y-3">

                            <div>
                                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" x-model="form.contact.fullName"
                                    :class="formErrors.fullName ? 'border-red-400 dark:border-red-500' : ''"
                                    class="co-field" placeholder="e.g. Amara Okafor">
                                <p x-show="formErrors.fullName" x-text="formErrors.fullName" class="text-[10px] text-red-500 mt-1"></p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Email Address <span class="text-red-500">*</span></label>
                                    <input type="email" x-model="form.contact.email"
                                        :class="formErrors.email ? 'border-red-400 dark:border-red-500' : ''"
                                        class="co-field" placeholder="you@example.com">
                                    <p x-show="formErrors.email" x-text="formErrors.email" class="text-[10px] text-red-500 mt-1"></p>
                                    <p x-show="!formErrors.email" class="text-[10px] text-neutral-400 dark:text-neutral-500 mt-1">Order updates sent here</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Phone Number <span class="text-red-500">*</span></label>
                                    <div class="flex">
                                        <select x-model="form.contact.phoneCode" class="co-field co-field-muted shrink-0"
                                            style="width:80px;border-right:none;padding-right:24px;background-position:right 6px center">
                                            <option value="+234">🇳🇬 +234</option>
                                            <option value="+1">🇺🇸 +1</option>
                                            <option value="+44">🇬🇧 +44</option>
                                            <option value="+233">🇬🇭 +233</option>
                                            <option value="+1-CA">🇨🇦 +1</option>
                                            <option value="+237">🇨🇲 +237</option>
                                            <option value="+27">🇿🇦 +27</option>
                                        </select>
                                        <input type="tel" x-model="form.contact.phone"
                                            :class="formErrors.phone ? 'border-red-400 dark:border-red-500' : ''"
                                            class="co-field" style="flex:1;min-width:0" placeholder="08012345678">
                                    </div>
                                    <p x-show="formErrors.phone" x-text="formErrors.phone" class="text-[10px] text-red-500 mt-1"></p>
                                </div>
                            </div>

                            {{-- Guest password --}}
                            <template x-if="isGuest">
                                <div>
                                    <div class="co-notice-brand flex items-start gap-2 px-3 py-2.5 mb-3" style="background:#E6F3F2;border:1px solid #CCE7E4">
                                        <svg class="w-3.5 h-3.5 text-brand mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-[10px] leading-relaxed text-brand-700 dark:text-brand-200">
                                            A customer account will be created automatically when you complete your order. Set a password now to access it, or leave blank — we'll email you a temporary one.
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Password <span class="text-neutral-400 font-normal">(optional)</span></label>
                                            <div class="relative">
                                                <input :type="showPassword ? 'text' : 'password'"
                                                    x-model="form.contact.password"
                                                    class="co-field" style="padding-right:36px"
                                                    placeholder="Min. 8 characters">
                                                <button type="button" @click="showPassword = !showPassword"
                                                    class="absolute right-2.5 top-2 text-neutral-400 hover:text-neutral-600 transition-colors">
                                                    <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Confirm Password</label>
                                            <input :type="showPassword ? 'text' : 'password'"
                                                x-model="form.contact.confirmPassword"
                                                class="co-field" placeholder="Re-enter password">
                                            <p x-show="form.contact.password && form.contact.confirmPassword && form.contact.password !== form.contact.confirmPassword"
                                                class="text-[10px] text-red-500 mt-1">
                                                Passwords do not match
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </div>

                    {{-- Shipping Address --}}
                    <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700">
                        <div class="px-5 py-3 border-b border-neutral-200 dark:border-neutral-700">
                            <h2 class="font-display font-semibold text-neutral-900 dark:text-neutral-50 text-sm">Shipping Address</h2>
                        </div>
                        <div class="px-5 py-4 space-y-3">

                            <div>
                                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Country <span class="text-red-500">*</span></label>
                                <select x-model="form.address.country" @change="onCountryChange()"
                                    :class="formErrors.country ? 'border-red-400 dark:border-red-500' : ''"
                                    class="co-field">
                                    <option value="">Select country</option>
                                    <template x-for="c in countries" :key="c.code">
                                        <option :value="c.code" x-text="c.flag + '  ' + c.name"></option>
                                    </template>
                                </select>
                                <p x-show="formErrors.country" x-text="formErrors.country" class="text-[10px] text-red-500 mt-1"></p>
                            </div>

                            <div class="relative">
                                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Street Address <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text"
                                        x-model="form.address.street"
                                        @input="searchAddress($event.target.value)"
                                        @focus="showSuggestions = addressSuggestions.length > 0"
                                        @blur="setTimeout(() => showSuggestions = false, 250)"
                                        :class="formErrors.street ? 'border-red-400 dark:border-red-500' : ''"
                                        class="co-field"
                                        style="padding-right:30px"
                                        placeholder="Start typing your street address…">
                                    <div x-show="addrLoading" class="absolute right-2.5 top-2.5">
                                        <svg class="w-3.5 h-3.5 text-brand animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div x-show="showSuggestions && addressSuggestions.length > 0" x-cloak class="addr-drop">
                                    <template x-for="(s, i) in addressSuggestions" :key="i">
                                        <div class="addr-item" @mousedown.prevent="selectAddress(s)">
                                            <svg class="w-3.5 h-3.5 text-brand shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium text-neutral-900 truncate" x-text="s.main"></p>
                                                <p class="text-[10px] text-neutral-400 truncate" x-text="s.secondary"></p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <p class="text-[10px] text-neutral-400 dark:text-neutral-500 mt-1">
                                    Powered by OpenStreetMap · free · no API key required
                                </p>
                                <p x-show="formErrors.street" x-text="formErrors.street" class="text-[10px] text-red-500 mt-1"></p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">House / Apt No <span class="text-neutral-400 font-normal">(optional)</span></label>
                                <input type="text" x-model="form.address.houseNo" class="co-field" placeholder="e.g. Flat 3B, Suite 101">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">State <span class="text-red-500">*</span></label>
                                    <template x-if="form.address.country === 'NG'">
                                        <select x-model="form.address.state" @change="form.address.city = ''; fetchShippingOptions()" class="co-field">
                                            <option value="">Select state</option>
                                            <template x-for="s in nigeriaStates" :key="s">
                                                <option :value="s" x-text="s"></option>
                                            </template>
                                        </select>
                                    </template>
                                    <template x-if="form.address.country !== 'NG'">
                                        <input type="text" x-model="form.address.state" class="co-field" placeholder="State / Province">
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">City <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="form.address.city" @change="fetchShippingOptions()"
                                        :class="formErrors.city ? 'border-red-400 dark:border-red-500' : ''"
                                        class="co-field" placeholder="e.g. Lagos">
                                    <p x-show="formErrors.city" x-text="formErrors.city" class="text-[10px] text-red-500 mt-1"></p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">
                                    Postal / Zip Code
                                    <span class="text-neutral-400 font-normal"
                                          x-text="form.address.country === 'NG' ? '(optional for Nigeria)' : '(required)'"></span>
                                </label>
                                <input type="text" x-model="form.address.postal" @change="fetchShippingOptions()" class="co-field"
                                    :placeholder="form.address.country === 'NG' ? 'e.g. 100001' : 'e.g. SW1A 1AA'">
                                <p x-show="form.address.country === 'NG'" class="text-[10px] text-neutral-400 dark:text-neutral-500 mt-1">
                                    Nigerian postal codes are optional but help with faster delivery routing.
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Delivery Notes <span class="text-neutral-400 font-normal">(optional)</span></label>
                                <textarea x-model="form.address.notes" rows="2"
                                    class="co-field" style="resize:none"
                                    placeholder="Landmark, gate colour, access instructions…"></textarea>
                            </div>

                        </div>
                    </div>

                    {{-- Shipping Method --}}
                    <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700">
                        <div class="px-5 py-3 border-b border-neutral-200 dark:border-neutral-700">
                            <h2 class="font-display font-semibold text-neutral-900 dark:text-neutral-50 text-sm">Shipping Method</h2>
                        </div>
                        <div class="px-5 py-4">

                            {{-- Loading --}}
                            <template x-if="shippingLoading">
                                <div class="flex items-center gap-2 py-4 text-sm text-neutral-400">
                                    <svg class="w-4 h-4 animate-spin text-brand" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                                    Calculating shipping rates…
                                </div>
                            </template>

                            {{-- No methods yet --}}
                            <template x-if="!shippingLoading && shippingMethods.length === 0">
                                <p class="text-xs text-neutral-400 dark:text-neutral-500 py-3">
                                    Enter your country, state, and city above to see available shipping options.
                                </p>
                            </template>

                            {{-- Methods list --}}
                            <template x-if="!shippingLoading && shippingMethods.length > 0">
                                <div class="space-y-2">
                                    <template x-for="method in shippingMethods" :key="method.id">
                                        <label class="ship-card" :class="form.shippingMethod === method.id ? 'selected' : ''">
                                            <input type="radio" x-model="form.shippingMethod" :value="method.id" class="shrink-0 w-4 h-4">
                                            <input type="radio" x-model="form.shippingMethod" :value="method.id" class="shrink-0 w-4 h-4">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-medium text-neutral-900 dark:text-neutral-50" x-text="method.name"></span>
                                                    <template x-if="method.badge">
                                                        <span :class="method.badgeCls" x-text="method.badge"></span>
                                                    </template>
                                                </div>
                                                <p class="text-[10px] text-neutral-500 dark:text-neutral-400 mt-0.5" x-text="method.description"></p>
                                            </div>
                                            <span class="text-sm font-semibold text-neutral-900 dark:text-neutral-50 shrink-0">
                                                <template x-if="method.contact_required"><span class="text-accent-600 dark:text-accent-400">Contact us</span></template>
                                                <template x-if="!method.contact_required"><span x-text="method.price === 0 ? 'Free' : fmt(method.price)"></span></template>
                                            </span>
                                        </label>
                                    </template>
                                </div>
                            </template>

                        </div>
                    </div>

                    <div class="space-y-2">
                        <template x-if="formErrors.shippingMethod">
                            <p class="text-xs text-red-500 text-right" x-text="formErrors.shippingMethod"></p>
                        </template>
                        <div class="flex justify-end">
                            <button @click="goToPayment()"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand text-white text-sm font-semibold hover:bg-brand-600 transition-colors">
                                Continue to Payment
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                </div>
                </template>

                {{-- ═══ STEP 2 · PAYMENT ══════════════════════════════════ --}}
                <template x-if="currentStep === 2">
                <div class="space-y-3 step-panel">

                    <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 px-5 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-3.5 h-3.5 text-brand shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <div>
                                <p class="text-[10px] text-neutral-400 dark:text-neutral-500 leading-none mb-0.5">Delivering to</p>
                                <p class="text-sm font-medium text-neutral-900"
                                   x-text="(form.address.street || '—') + (form.address.city ? ', ' + form.address.city : '')"></p>
                            </div>
                        </div>
                        <button @click="goTo(1)" class="text-xs font-medium text-brand dark:text-brand-300 hover:text-brand-600 dark:hover:text-brand-200 transition-colors">Change</button>
                    </div>

                    <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700">
                        <div class="px-5 py-3 border-b border-neutral-200 dark:border-neutral-700">
                            <h2 class="font-display font-semibold text-neutral-900 dark:text-neutral-50 text-sm">Payment Method</h2>
                        </div>
                        <div class="px-5 py-4 space-y-3">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Choose a gateway. You'll complete payment securely on their platform.</p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="pay-card" :class="form.paymentMethod === 'flutterwave' ? 'selected' : ''">
                                    <input type="radio" x-model="form.paymentMethod" value="flutterwave" class="sr-only">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 flex items-center justify-center text-white font-bold text-xs shrink-0" style="background:#FF5733;font-family:Manrope,sans-serif">Fw</div>
                                        <div>
                                            <p class="text-sm font-semibold text-neutral-900 dark:text-neutral-50">Flutterwave</p>
                                            <p class="text-[10px] text-neutral-400 dark:text-neutral-500 mt-0.5">Card · Bank · USSD · Opay · Kuda</p>
                                        </div>
                                    </div>
                                    <div x-show="form.paymentMethod === 'flutterwave'"
                                         class="absolute top-2 right-2 w-4 h-4 bg-brand rounded-full flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </label>

                                <label class="pay-card" :class="form.paymentMethod === 'paystack' ? 'selected' : ''">
                                    <input type="radio" x-model="form.paymentMethod" value="paystack" class="sr-only">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 flex items-center justify-center text-white font-bold text-xs shrink-0" style="background:#0BA4DB;font-family:Manrope,sans-serif">Ps</div>
                                        <div>
                                            <p class="text-sm font-semibold text-neutral-900 dark:text-neutral-50">Paystack</p>
                                            <p class="text-[10px] text-neutral-400 dark:text-neutral-500 mt-0.5">Card · Bank Transfer · USSD · QR</p>
                                        </div>
                                    </div>
                                    <div x-show="form.paymentMethod === 'paystack'"
                                         class="absolute top-2 right-2 w-4 h-4 bg-brand rounded-full flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </label>
                            </div>

                            <template x-if="form.paymentMethod">
                                <div class="flex items-start gap-2 px-3 py-2.5 bg-neutral-50 dark:bg-neutral-700 border border-neutral-200 dark:border-neutral-600">
                                    <svg class="w-3.5 h-3.5 text-brand mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <p class="text-[10px] text-neutral-600 dark:text-neutral-300">
                                        You'll be securely redirected to
                                        <strong x-text="form.paymentMethod === 'flutterwave' ? 'Flutterwave' : 'Paystack'"></strong>
                                        to complete payment. Your order is confirmed once payment is verified.
                                    </p>
                                </div>
                            </template>

                            <div class="pt-2 border-t border-neutral-200 dark:border-neutral-700 flex items-center justify-between">
                                <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Total to pay</span>
                                <span class="font-display font-bold text-neutral-900 text-base" x-text="fmt(getTotal())"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Error message --}}
                    <template x-if="orderError">
                        <div class="flex items-center gap-2 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-sm text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="orderError"></span>
                        </div>
                    </template>

                    <div class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-[10px] text-neutral-400 dark:text-neutral-500">
                        <svg class="w-3.5 h-3.5 text-brand shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        256-bit SSL encryption · PCI-DSS compliant · We never store your card details
                    </div>

                    <div class="flex items-center justify-between">
                        <button @click="goTo(1)"
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Back to Shipping
                        </button>
                        <button @click="placeOrder()"
                            :disabled="!form.paymentMethod || placingOrder"
                            :class="(!form.paymentMethod || placingOrder) ? 'opacity-40 cursor-not-allowed' : 'hover:bg-brand-600'"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand text-white text-sm font-semibold transition-colors">
                            <template x-if="placingOrder">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            </template>
                            <template x-if="!placingOrder">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </template>
                            <span x-text="placingOrder ? 'Processing…' : 'Place Order & Pay'"></span>
                        </button>
                    </div>

                </div>
                </template>

                {{-- ═══ STEP 3 · ORDER CONFIRMED ══════════════════════════ --}}
                <template x-if="currentStep === 3">
                <div class="step-panel">
                    <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 px-6 py-10">

                        <div class="w-14 h-14 bg-brand-50 border border-brand-200 rounded-full flex items-center justify-center mx-auto mb-4 success-icon">
                            <svg class="w-7 h-7 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>

                        <div class="text-center mb-6">
                            <h1 class="font-display font-bold text-neutral-900 dark:text-neutral-50 mb-1" style="font-size:20px">Order Confirmed!</h1>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                Thank you, <span class="font-medium text-neutral-800 dark:text-neutral-200" x-text="form.contact.fullName || 'valued customer'"></span>.
                                Your order has been placed and is being processed.
                            </p>
                            <div class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-neutral-50 dark:bg-neutral-700 border border-neutral-200 dark:border-neutral-600">
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">Order</span>
                                <span class="font-mono font-semibold text-sm text-neutral-900 dark:text-neutral-50" x-text="orderNumber"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                            <div class="p-3 border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-700">
                                <div class="w-6 h-6 bg-brand-100 dark:bg-brand-900 border border-brand-200 dark:border-brand-800 flex items-center justify-center mb-2"><svg class="w-3 h-3 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                                <p class="text-xs font-semibold text-neutral-800 dark:text-neutral-100 mb-0.5">Confirmation Email</p>
                                <p class="text-[10px] text-neutral-500 dark:text-neutral-400 leading-relaxed">Receipt and order details sent to your email address.</p>
                            </div>
                            <div class="p-3 border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-700">
                                <div class="w-6 h-6 bg-brand-100 dark:bg-brand-900 border border-brand-200 dark:border-brand-800 flex items-center justify-center mb-2"><svg class="w-3 h-3 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
                                <p class="text-xs font-semibold text-neutral-800 dark:text-neutral-100 mb-0.5">Processing</p>
                                <p class="text-[10px] text-neutral-500 dark:text-neutral-400 leading-relaxed">Packed and dispatched within 1–2 business days.</p>
                            </div>
                            <div class="p-3 border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-700">
                                <div class="w-6 h-6 bg-brand-100 dark:bg-brand-900 border border-brand-200 dark:border-brand-800 flex items-center justify-center mb-2"><svg class="w-3 h-3 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg></div>
                                <p class="text-xs font-semibold text-neutral-800 dark:text-neutral-100 mb-0.5">Delivery</p>
                                <p class="text-[10px] text-neutral-500 dark:text-neutral-400 leading-relaxed">Est. delivery per your selected shipping method.</p>
                            </div>
                        </div>

                        <template x-if="isGuest">
                            <div class="co-notice-accent flex items-start gap-2 px-3 py-2.5 mb-6" style="background:#FBF4E1;border:1px solid #EFD187">
                                <svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-accent-700 dark:text-accent-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <p class="text-[10px] leading-relaxed text-accent-700 dark:text-accent-300">
                                    A customer account has been created at <strong x-text="form.contact.email || 'your email'"></strong>.
                                    Check your inbox for login credentials to track and manage your orders.
                                </p>
                            </div>
                        </template>

                        <div class="flex flex-col sm:flex-row gap-2 justify-center">
                            <a href="{{ route('shop.index') }}"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-brand text-white text-sm font-semibold hover:bg-brand-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                Continue Shopping
                            </a>
                        </div>

                    </div>
                </div>
                </template>

            </div>{{-- /left --}}

            {{-- ════════════════════════════════════════════════════════════
                 RIGHT — ORDER SUMMARY
            ════════════════════════════════════════════════════════════════ --}}
            <div class="lg:col-span-5" :class="currentStep === 3 ? 'hidden lg:block' : ''">
                <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 sticky top-16">

                    <button @click="summaryOpen = !summaryOpen"
                        class="w-full px-5 py-3 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-4H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <h2 class="font-display font-semibold text-neutral-900 dark:text-neutral-50 text-sm">Order Summary</h2>
                            <span class="text-[10px] bg-neutral-100 dark:bg-neutral-700 text-neutral-500 dark:text-neutral-400 px-1.5 py-0.5 font-semibold"
                                  x-text="cart.items.length + ' item' + (cart.items.length !== 1 ? 's' : '')"></span>
                        </div>
                        <div class="flex items-center gap-2 lg:hidden">
                            <span class="text-sm font-bold text-neutral-900" x-text="fmt(getTotal())"></span>
                            <svg class="w-4 h-4 text-neutral-400 transition-transform" :class="summaryOpen ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </button>

                    <div x-show="summaryOpen" class="divide-y divide-neutral-100 dark:divide-neutral-700">

                        {{-- ── Cart Items ──────────────────────────────── --}}
                        <div class="px-5 py-3 space-y-3">
                            <template x-for="item in cart.items" :key="item.id">
                                <div class="flex gap-3">
                                    <div class="relative shrink-0">
                                        <div class="w-14 h-14 border border-neutral-200 dark:border-neutral-700 bg-neutral-100 dark:bg-neutral-700 flex items-center justify-center overflow-hidden">
                                            <template x-if="item.image">
                                                <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!item.image">
                                                <svg class="w-5 h-5 text-neutral-300 dark:text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </template>
                                        </div>
                                        <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-neutral-700 text-white text-[10px] font-bold flex items-center justify-center rounded-full"
                                              x-text="item.qty"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-neutral-900 dark:text-neutral-50 leading-tight" x-text="item.name"></p>
                                        <template x-if="item.variant">
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <div class="w-2.5 h-2.5 rounded-full border border-neutral-300" :style="'background:' + item.variant.hex"></div>
                                                <span class="text-[10px] text-neutral-400 dark:text-neutral-500" x-text="item.variant.name"></span>
                                            </div>
                                        </template>
                                        <p class="text-[10px] text-neutral-400 dark:text-neutral-500 mt-0.5" x-text="unitLabel(item)"></p>
                                        <div class="flex items-center mt-1.5 gap-0">
                                            <button @click="decQty(item)" class="qty-btn">−</button>
                                            <div class="qty-display" x-text="item.qty"></div>
                                            <button @click="incQty(item)" class="qty-btn">+</button>
                                            <button @click="removeItem(item)" class="ml-2 text-neutral-300 dark:text-neutral-600 hover:text-red-400 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-sm font-semibold text-neutral-900 dark:text-neutral-50" x-text="fmt(item.price * item.qty)"></p>
                                        <p class="text-[10px] text-neutral-400 dark:text-neutral-500" x-text="fmt(item.price) + '/unit'"></p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- ── Add-ons ─────────────────────────────────── --}}
                        <template x-if="cart.addOns && cart.addOns.length > 0">
                            <div class="px-5 py-3 space-y-3">
                                <p class="text-[10px] font-semibold text-neutral-400 dark:text-neutral-500 tracking-widest uppercase">Add-ons</p>
                                <template x-for="addon in cart.addOns" :key="addon.id">
                                    <div class="flex gap-3">
                                        <div class="w-14 h-14 border border-neutral-200 dark:border-neutral-700 bg-neutral-100 dark:bg-neutral-700 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-neutral-300 dark:text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-neutral-900 dark:text-neutral-50 leading-tight" x-text="addon.name"></p>
                                            <p class="text-[10px] text-neutral-400 dark:text-neutral-500 mt-0.5" x-text="unitLabel(addon)"></p>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <p class="text-sm font-semibold text-neutral-900 dark:text-neutral-50" x-text="fmt(addon.price * (addon.qty || 1))"></p>
                                            <button @click="removeAddon(addon)"
                                                class="text-[10px] text-neutral-400 dark:text-neutral-500 hover:text-red-400 dark:hover:text-red-400 transition-colors mt-0.5">
                                                remove
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- ── Promo Code ──────────────────────────────── --}}
                        <div class="px-5 py-3" x-show="currentStep < 3">
                            <template x-if="!promoApplied">
                                <div>
                                    <div class="flex gap-0">
                                        <input type="text" x-model="promoCode"
                                            @keydown.enter="applyPromo()"
                                            placeholder="Promo / coupon code"
                                            class="co-field flex-1 min-w-0"
                                            style="border-right:none;text-transform:uppercase;font-size:12px">
                                        <button @click="applyPromo()"
                                            class="px-3 py-2 bg-neutral-900 text-white text-xs font-semibold hover:bg-neutral-800 transition-colors whitespace-nowrap">
                                            Apply
                                        </button>
                                    </div>
                                    <p x-show="promoError" class="text-[10px] text-red-500 mt-1" x-text="promoError"></p>
                                </div>
                            </template>
                            <template x-if="promoApplied">
                                <div class="co-notice-brand flex items-center justify-between px-3 py-2" style="background:#E6F3F2;border:1px solid #99CFC9">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        <span class="text-xs font-semibold text-brand dark:text-brand-300"
                                              x-text="promoCode.toUpperCase() + ' — ' + promoDiscount + '% off'"></span>
                                    </div>
                                    <button @click="clearPromo()" class="text-[10px] font-medium text-brand hover:text-brand-600">Remove</button>
                                </div>
                            </template>
                        </div>

                        {{-- ── Totals ──────────────────────────────────── --}}
                        <div class="px-5 py-3 space-y-2">
                            <div class="flex justify-between text-xs text-neutral-500 dark:text-neutral-400">
                                <span>Subtotal</span><span x-text="fmt(subtotal())"></span>
                            </div>
                            <template x-if="cart.addOns && cart.addOns.length > 0">
                                <div class="flex justify-between text-xs text-neutral-500 dark:text-neutral-400">
                                    <span>Add-ons</span><span x-text="fmt(addOnsTotal())"></span>
                                </div>
                            </template>
                            <template x-if="promoApplied">
                                <div class="flex justify-between text-xs text-brand">
                                    <span>Discount (<span x-text="promoDiscount"></span>%)</span>
                                    <span>− <span x-text="fmt(promoSavings())"></span></span>
                                </div>
                            </template>
                            <div class="flex justify-between text-xs text-neutral-500 dark:text-neutral-400">
                                <span>Shipping</span>
                                <span x-text="shippingLoading ? 'Calculating…' : (shippingCost() === 0 ? (form.shippingMethod ? 'Free' : 'TBD') : fmt(shippingCost()))"></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-neutral-200 dark:border-neutral-700">
                                <span class="text-sm font-semibold text-neutral-800 dark:text-neutral-100">Total</span>
                                <span class="font-display font-bold text-neutral-900" style="font-size:15px" x-text="fmt(getTotal())"></span>
                            </div>
                            <p class="text-[10px] text-neutral-400 dark:text-neutral-500">All prices in <span x-text="Alpine.store('currency')?.active || 'NGN'"></span></p>
                        </div>

                        {{-- ── Trust badges ────────────────────────────── --}}
                        <div class="px-5 py-3 flex items-center gap-5 flex-wrap border-t border-neutral-100 dark:border-neutral-700" x-show="currentStep < 3">
                            <div class="flex items-center gap-1 text-[10px] text-neutral-400 dark:text-neutral-500">
                                <svg class="w-3 h-3 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                SSL Secured
                            </div>
                            <div class="flex items-center gap-1 text-[10px] text-neutral-400 dark:text-neutral-500">
                                <svg class="w-3 h-3 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                PCI-DSS
                            </div>
                            <div class="flex items-center gap-1 text-[10px] text-neutral-400 dark:text-neutral-500">
                                <svg class="w-3 h-3 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                All cards
                            </div>
                        </div>

                    </div>{{-- /summaryOpen --}}
                </div>
            </div>{{-- /right --}}

        </div>{{-- /grid --}}
    </div>{{-- /container --}}
</div>{{-- /x-data checkoutApp --}}

</div>{{-- /livewire wrapper --}}
