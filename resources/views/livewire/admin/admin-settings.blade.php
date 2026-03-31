<div x-data="{ tab: 'general', confirmMaintenance: false }">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Settings</h1>
            <p class="text-sm text-gray-400 dark:text-white/40 mt-0.5">Manage your store configuration</p>
        </div>
    </div>

    {{-- Tab navigation --}}
    <div class="flex items-center gap-1 bg-gray-100 dark:bg-white/[0.05] rounded-2xl p-1 mb-6 overflow-x-auto scrollbar-none">
        @foreach([
            'general'  => ['label' => 'General',  'icon' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>'],
            'email'    => ['label' => 'Email',     'icon' => '<path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'],
            'store'    => ['label' => 'Store',     'icon' => '<path d="M1 3h22l-2 9H3L1 3zm2 9v8a2 2 0 002 2h14a2 2 0 002-2v-8"/><path d="M9 3v9m6-9v9"/>'],
            'seo'      => ['label' => 'SEO',       'icon' => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>'],
            'security' => ['label' => 'Security',  'icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>'],
        ] as $tab => $cfg)
            <button @click="tab = '{{ $tab }}'" type="button"
                    class="flex items-center gap-2 px-4 py-2 text-[13px] font-medium rounded-xl whitespace-nowrap transition-all duration-150 shrink-0"
                    :class="tab === '{{ $tab }}' ? 'bg-white dark:bg-[#1C1F27] text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-white/40 hover:text-gray-700 dark:hover:text-white/70'">
                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">{!! $cfg['icon'] !!}</svg>
                {{ $cfg['label'] }}
            </button>
        @endforeach
    </div>

    {{-- ═══════════════════════════════════════════════════════  GENERAL TAB  --}}
    <div x-show="tab === 'general'" class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-gray-100 dark:border-white/[0.06]">
            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
            <div>
                <h2 class="text-[15px] font-semibold text-gray-900 dark:text-white">General Settings</h2>
                <p class="text-xs text-gray-400 dark:text-white/40 mt-0.5">Basic store identity &amp; contact details</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            {{-- Store Name --}}
            <div class="sm:col-span-2 lg:col-span-1">
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40 mb-1.5">Store Name</label>
                <input wire:model="store_name" type="text" placeholder="1st Delightsome"
                       class="w-full bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors"/>
                @error('store_name') <p class="mt-1 text-[11px] text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Store Tagline --}}
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40 mb-1.5">Tagline</label>
                <input wire:model="store_tagline" type="text" placeholder="Premium Fabrics & Textiles"
                       class="w-full bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors"/>
            </div>

            {{-- Store Email --}}
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40 mb-1.5">Store Email</label>
                <input wire:model="store_email" type="email" placeholder="hello@yourstore.com"
                       class="w-full bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors"/>
                @error('store_email') <p class="mt-1 text-[11px] text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Store Phone --}}
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40 mb-1.5">Phone</label>
                <input wire:model="store_phone" type="text" placeholder="+234 800 000 0000"
                       class="w-full bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors"/>
            </div>

            {{-- Store Address --}}
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40 mb-1.5">Address</label>
                <input wire:model="store_address" type="text" placeholder="30b Opebi Rd, Opebi"
                       class="w-full bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors"/>
            </div>

            {{-- City --}}
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40 mb-1.5">City</label>
                <input wire:model="store_city" type="text" placeholder="Ikeja, Lagos"
                       class="w-full bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors"/>
            </div>

            {{-- Country --}}
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40 mb-1.5">Country</label>
                <input wire:model="store_country" type="text" placeholder="Nigeria"
                       class="w-full bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors"/>
            </div>
        </div>

        <div class="flex justify-end mt-6 pt-5 border-t border-gray-100 dark:border-white/[0.06]">
            <button wire:click="saveGeneral" type="button"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 text-black text-sm font-semibold rounded-xl transition-colors">
                <span wire:loading wire:target="saveGeneral" class="w-3.5 h-3.5 border-2 border-black/30 border-t-black rounded-full animate-spin"></span>
                <svg wire:loading.remove wire:target="saveGeneral" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save General Settings
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════  EMAIL TAB  --}}
    <div x-show="tab === 'email'" class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-gray-100 dark:border-white/[0.06]">
            <div class="w-9 h-9 rounded-xl bg-blue-500/10 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-[15px] font-semibold text-gray-900 dark:text-white">Email &amp; Notifications</h2>
                <p class="text-xs text-gray-400 dark:text-white/40 mt-0.5">Configure outgoing mail sender and alert preferences</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
            {{-- Mail From Name --}}
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40 mb-1.5">Sender Name</label>
                <input wire:model="mail_from_name" type="text" placeholder="1st Delightsome"
                       class="w-full bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors"/>
                @error('mail_from_name') <p class="mt-1 text-[11px] text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Mail From Address --}}
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40 mb-1.5">Sender Email</label>
                <input wire:model="mail_from_address" type="email" placeholder="hello@yourstore.com"
                       class="w-full bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors"/>
                @error('mail_from_address') <p class="mt-1 text-[11px] text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Admin Notification Email --}}
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40 mb-1.5">Admin Notification Email</label>
                <input wire:model="admin_notification_email" type="email" placeholder="admin@yourstore.com"
                       class="w-full bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors"/>
                <p class="mt-1 text-[11px] text-gray-400 dark:text-white/30">System alerts are sent to this address</p>
                @error('admin_notification_email') <p class="mt-1 text-[11px] text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Notification Toggles --}}
        <div class="bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/[0.05] rounded-xl p-4">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40 mb-4">Notify Admin When</p>
            <div class="space-y-3.5">
                @foreach([
                    'notify_new_order'      => ['New order is placed',         'bg-emerald-500'],
                    'notify_new_contact'    => ['New contact form submitted',   'bg-blue-500'],
                    'notify_new_subscriber' => ['New newsletter subscriber',    'bg-purple-500'],
                    'notify_low_stock'      => ['Product stock is running low', 'bg-amber-500'],
                ] as $prop => [$label, $color])
                <div class="flex items-center justify-between gap-4">
                    <span class="text-sm text-gray-700 dark:text-white/70">{{ $label }}</span>
                    <button type="button" wire:click="$toggle('{{ $prop }}')" role="switch"
                            class="relative inline-flex h-[22px] w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none
                                   {{ $this->$prop ? $color : 'bg-gray-300 dark:bg-white/[0.12]' }}">
                        <span class="pointer-events-none inline-block h-[18px] w-[18px] rounded-full bg-white shadow transform transition-transform duration-200 ease-in-out
                                     {{ $this->$prop ? 'translate-x-[18px]' : 'translate-x-0' }}"></span>
                    </button>
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end mt-6 pt-5 border-t border-gray-100 dark:border-white/[0.06]">
            <button wire:click="saveEmail" type="button"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 text-black text-sm font-semibold rounded-xl transition-colors">
                <span wire:loading wire:target="saveEmail" class="w-3.5 h-3.5 border-2 border-black/30 border-t-black rounded-full animate-spin"></span>
                <svg wire:loading.remove wire:target="saveEmail" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Email Settings
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════  STORE TAB  --}}
    <div x-show="tab === 'store'" class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-gray-100 dark:border-white/[0.06]">
            <div class="w-9 h-9 rounded-xl bg-amber-500/10 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M1 3h22l-2 9H3L1 3zm2 9v8a2 2 0 002 2h14a2 2 0 002-2v-8"/>
                </svg>
            </div>
            <div>
                <h2 class="text-[15px] font-semibold text-gray-900 dark:text-white">Store Configuration</h2>
                <p class="text-xs text-gray-400 dark:text-white/40 mt-0.5">Timezone, currency, status &amp; maintenance</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">

            {{-- Store Status --}}
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40 mb-1.5">Store Status</label>
                <div x-data="{
                        open: false,
                        options: [
                            { value: 'open',           label: 'Open',           desc: 'Accepting orders normally' },
                            { value: 'closed',         label: 'Closed',         desc: 'Not accepting orders' },
                            { value: 'by_appointment', label: 'By Appointment', desc: 'Orders by request only' },
                        ],
                        get selected() { return this.options.find(o => o.value === $wire.store_status) ?? this.options[0]; }
                     }" class="relative">
                    <button @click="open = !open" type="button"
                            class="w-full flex items-center justify-between gap-2 bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span :class="{
                                'bg-emerald-500': selected.value === 'open',
                                'bg-red-500': selected.value === 'closed',
                                'bg-amber-500': selected.value === 'by_appointment'
                            }" class="w-2 h-2 rounded-full shrink-0"></span>
                            <span x-text="selected.label" class="font-medium truncate"></span>
                        </div>
                        <svg :class="{ 'rotate-180': open }" class="w-4 h-4 text-gray-400 dark:text-white/30 shrink-0 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="absolute z-20 mt-1.5 w-full bg-white dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.08] rounded-xl shadow-xl overflow-hidden" style="display:none">
                        <template x-for="opt in options" :key="opt.value">
                            <button @click="$wire.set('store_status', opt.value); open = false" type="button"
                                    class="w-full flex items-start gap-3 px-3.5 py-2.5 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-colors text-left">
                                <span :class="{
                                    'bg-emerald-500': opt.value === 'open',
                                    'bg-red-500': opt.value === 'closed',
                                    'bg-amber-500': opt.value === 'by_appointment'
                                }" class="w-2 h-2 rounded-full mt-1.5 shrink-0"></span>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white/90" x-text="opt.label"></p>
                                    <p class="text-[11px] text-gray-400 dark:text-white/30 mt-0.5" x-text="opt.desc"></p>
                                </div>
                                <svg x-show="$wire.store_status === opt.value" class="w-4 h-4 text-emerald-400 ml-auto shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Timezone --}}
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40 mb-1.5">Timezone</label>
                <div x-data="{
                        open: false,
                        search: '',
                        zones: [
                            'Africa/Lagos','Africa/Abidjan','Africa/Accra','Africa/Cairo','Africa/Johannesburg','Africa/Nairobi',
                            'America/New_York','America/Chicago','America/Denver','America/Los_Angeles','America/Toronto',
                            'Europe/London','Europe/Paris','Europe/Berlin','Europe/Amsterdam','Europe/Madrid',
                            'Asia/Dubai','Asia/Kolkata','Asia/Singapore','Asia/Tokyo','Asia/Shanghai',
                            'Australia/Sydney','Pacific/Auckland','UTC'
                        ],
                        get filtered() { return this.search ? this.zones.filter(z => z.toLowerCase().includes(this.search.toLowerCase())) : this.zones; }
                     }" class="relative">
                    <button @click="open = !open; if(open) $nextTick(() => $refs.tzSearch.focus())" type="button"
                            class="w-full flex items-center justify-between gap-2 bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors">
                        <span class="truncate" x-text="$wire.store_timezone || 'Select timezone'"></span>
                        <svg :class="{ 'rotate-180': open }" class="w-4 h-4 text-gray-400 dark:text-white/30 shrink-0 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false; search = ''"
                         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="absolute z-20 mt-1.5 w-full bg-white dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.08] rounded-xl shadow-xl overflow-hidden" style="display:none">
                        <div class="p-2 border-b border-gray-100 dark:border-white/[0.06]">
                            <input x-ref="tzSearch" x-model="search" type="text" placeholder="Search timezones..."
                                   class="w-full bg-gray-50 dark:bg-white/[0.05] text-gray-900 dark:text-white/80 rounded-lg px-3 py-2 text-xs placeholder-gray-400 dark:placeholder-white/20 focus:outline-none"/>
                        </div>
                        <div class="max-h-44 overflow-y-auto py-1">
                            <template x-for="zone in filtered" :key="zone">
                                <button @click="$wire.set('store_timezone', zone); open = false; search = ''" type="button"
                                        class="w-full flex items-center justify-between gap-2 px-3.5 py-2 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-colors text-left">
                                    <span class="text-sm text-gray-800 dark:text-white/80" x-text="zone"></span>
                                    <svg x-show="$wire.store_timezone === zone" class="w-3.5 h-3.5 text-emerald-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                                </button>
                            </template>
                            <p x-show="filtered.length === 0" class="px-3.5 py-2 text-xs text-gray-400 dark:text-white/30">No timezones found</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Currency --}}
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40 mb-1.5">Currency</label>
                <div x-data="{
                        open: false,
                        currencies: [
                            { code: 'NGN', symbol: '₦', name: 'Nigerian Naira' },
                            { code: 'USD', symbol: '$', name: 'US Dollar' },
                            { code: 'EUR', symbol: '€', name: 'Euro' },
                            { code: 'GBP', symbol: '£', name: 'British Pound' },
                            { code: 'GHS', symbol: 'GH₵', name: 'Ghanaian Cedi' },
                            { code: 'KES', symbol: 'KSh', name: 'Kenyan Shilling' },
                            { code: 'ZAR', symbol: 'R', name: 'South African Rand' },
                        ],
                        get selected() { return this.currencies.find(c => c.code === $wire.store_currency) ?? this.currencies[0]; }
                     }" class="relative">
                    <button @click="open = !open" type="button"
                            class="w-full flex items-center justify-between gap-2 bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors">
                        <div class="flex items-center gap-2 min-w-0">
                            <span x-text="selected.symbol" class="text-xs font-bold text-gray-500 dark:text-white/40 shrink-0 w-6 text-center"></span>
                            <span x-text="selected.code + ' — ' + selected.name" class="truncate font-medium"></span>
                        </div>
                        <svg :class="{ 'rotate-180': open }" class="w-4 h-4 text-gray-400 dark:text-white/30 shrink-0 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="absolute z-20 mt-1.5 w-full bg-white dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.08] rounded-xl shadow-xl overflow-hidden py-1" style="display:none">
                        <template x-for="cur in currencies" :key="cur.code">
                            <button @click="$wire.set('store_currency', cur.code); $wire.set('currency_symbol', cur.symbol); open = false" type="button"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 hover:bg-gray-50 dark:hover:bg-white/[0.05] transition-colors text-left">
                                <span x-text="cur.symbol" class="text-xs font-bold text-gray-500 dark:text-white/40 w-6 text-center shrink-0"></span>
                                <div class="flex-1 min-w-0">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white/90" x-text="cur.code"></span>
                                    <span class="text-xs text-gray-400 dark:text-white/40 ml-1.5" x-text="cur.name"></span>
                                </div>
                                <svg x-show="$wire.store_currency === cur.code" class="w-3.5 h-3.5 text-emerald-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Currency Symbol --}}
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40 mb-1.5">Currency Symbol</label>
                <input wire:model="currency_symbol" type="text" placeholder="₦" maxlength="5"
                       class="w-full bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors"/>
                <p class="mt-1 text-[11px] text-gray-400 dark:text-white/30">Set automatically when you pick a currency</p>
            </div>

            {{-- Maintenance Mode with confirm --}}
            <div class="sm:col-span-2">
                <div class="flex items-start justify-between gap-4 p-4 bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-800/30 rounded-xl">
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Maintenance Mode</p>
                        <p class="text-xs text-gray-500 dark:text-white/40 mt-0.5">When enabled, visitors see a maintenance message. Admin access is unaffected.</p>
                    </div>
                    <button type="button"
                            @click="$wire.maintenance_mode ? $wire.$toggle('maintenance_mode') : (confirmMaintenance = true)"
                            role="switch"
                            class="relative inline-flex h-[22px] w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none
                                   {{ $maintenance_mode ? 'bg-red-500' : 'bg-gray-300 dark:bg-white/[0.12]' }}">
                        <span class="pointer-events-none inline-block h-[18px] w-[18px] rounded-full bg-white shadow transform transition-transform duration-200 ease-in-out
                                     {{ $maintenance_mode ? 'translate-x-[18px]' : 'translate-x-0' }}"></span>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex justify-end mt-2 pt-5 border-t border-gray-100 dark:border-white/[0.06]">
            <button wire:click="saveStore" type="button"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 text-black text-sm font-semibold rounded-xl transition-colors">
                <span wire:loading wire:target="saveStore" class="w-3.5 h-3.5 border-2 border-black/30 border-t-black rounded-full animate-spin"></span>
                <svg wire:loading.remove wire:target="saveStore" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Store Settings
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════  SEO TAB  --}}
    <div x-show="tab === 'seo'" class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-gray-100 dark:border-white/[0.06]">
            <div class="w-9 h-9 rounded-xl bg-violet-500/10 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-violet-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </div>
            <div>
                <h2 class="text-[15px] font-semibold text-gray-900 dark:text-white">SEO &amp; Metadata</h2>
                <p class="text-xs text-gray-400 dark:text-white/40 mt-0.5">Control how your store appears in search engines</p>
            </div>
        </div>

        <div class="space-y-5">
            {{-- SEO Title --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40">Meta Title</label>
                    <span class="text-[11px] text-gray-400 dark:text-white/30">{{ strlen($seo_title) }}/120</span>
                </div>
                <input wire:model.live="seo_title" type="text" placeholder="Your Store | Premium Products"
                       class="w-full bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors"/>
            </div>

            {{-- SEO Description --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40">Meta Description</label>
                    <span class="text-[11px] text-gray-400 dark:text-white/30">{{ strlen($seo_description) }}/300</span>
                </div>
                <textarea wire:model.live="seo_description" rows="3" placeholder="Describe your store for search engines..."
                          class="w-full bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors resize-none"></textarea>
            </div>

            {{-- SEO Keywords --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40">Keywords</label>
                    <span class="text-[11px] text-gray-400 dark:text-white/30">comma-separated</span>
                </div>
                <input wire:model="seo_keywords" type="text" placeholder="fabric, textiles, Lagos, aso-oke"
                       class="w-full bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors"/>
            </div>

            {{-- SERP Preview --}}
            @if($seo_title || $seo_description)
            <div class="p-4 bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/[0.05] rounded-xl">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/30 mb-3">Search Preview</p>
                <div class="space-y-0.5">
                    <p class="text-[13px] text-blue-600 dark:text-blue-400 font-medium truncate">{{ $seo_title ?: config('app.url') }}</p>
                    <p class="text-[11px] text-green-700 dark:text-green-600 truncate">{{ config('app.url') }}</p>
                    <p class="text-[12px] text-gray-600 dark:text-white/50 line-clamp-2">{{ $seo_description ?: 'No description set.' }}</p>
                </div>
            </div>
            @endif

            {{-- noindex --}}
            <div class="flex items-start justify-between gap-4 p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800/30 rounded-xl">
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Hide from Search Engines</p>
                    <p class="text-xs text-gray-500 dark:text-white/40 mt-0.5">Adds a <code class="font-mono text-amber-600 dark:text-amber-400">noindex</code> meta tag — search engines will not index your site.</p>
                </div>
                <button type="button" wire:click="$toggle('seo_noindex')" role="switch"
                        class="relative inline-flex h-[22px] w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none
                               {{ $seo_noindex ? 'bg-amber-500' : 'bg-gray-300 dark:bg-white/[0.12]' }}">
                    <span class="pointer-events-none inline-block h-[18px] w-[18px] rounded-full bg-white shadow transform transition-transform duration-200 ease-in-out
                                 {{ $seo_noindex ? 'translate-x-[18px]' : 'translate-x-0' }}"></span>
                </button>
            </div>
        </div>

        <div class="flex justify-end mt-6 pt-5 border-t border-gray-100 dark:border-white/[0.06]">
            <button wire:click="saveSeo" type="button"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 text-black text-sm font-semibold rounded-xl transition-colors">
                <span wire:loading wire:target="saveSeo" class="w-3.5 h-3.5 border-2 border-black/30 border-t-black rounded-full animate-spin"></span>
                <svg wire:loading.remove wire:target="saveSeo" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save SEO Settings
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════  SECURITY TAB  --}}
    <div x-show="tab === 'security'" class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-gray-100 dark:border-white/[0.06]">
            <div class="w-9 h-9 rounded-xl bg-red-500/10 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-[15px] font-semibold text-gray-900 dark:text-white">Security</h2>
                <p class="text-xs text-gray-400 dark:text-white/40 mt-0.5">Session management, verification &amp; HTTPS</p>
            </div>
        </div>

        <div class="space-y-5">
            {{-- Session Lifetime --}}
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/40 mb-1.5">Session Lifetime <span class="normal-case">(minutes)</span></label>
                <div class="flex items-center gap-3">
                    <input wire:model="session_lifetime" type="number" min="15" max="10080" step="15"
                           class="w-40 bg-gray-50 dark:bg-[#1C1F27] border border-gray-200 dark:border-white/[0.07] text-gray-900 dark:text-white/80 rounded-xl px-3.5 py-2.5 text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-emerald-500/60 focus:ring-2 focus:ring-emerald-500/10 transition-colors"/>
                    <span class="text-sm text-gray-400 dark:text-white/40">
                        ≈ @if($session_lifetime < 60) {{ $session_lifetime }} min @elseif($session_lifetime < 1440) {{ round($session_lifetime / 60, 1) }} hr @else {{ round($session_lifetime / 1440, 1) }} days @endif
                    </span>
                </div>
                @error('session_lifetime') <p class="mt-1 text-[11px] text-red-500">{{ $message }}</p> @enderror
                <p class="mt-1 text-[11px] text-gray-400 dark:text-white/30">Minimum 15 min · Maximum 10,080 min (7 days)</p>
            </div>

            {{-- Require Email Verification --}}
            <div class="flex items-start justify-between gap-4 p-4 bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/[0.05] rounded-xl">
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Require Email Verification</p>
                    <p class="text-xs text-gray-500 dark:text-white/40 mt-0.5">New customer accounts must verify their email before they can place orders.</p>
                </div>
                <button type="button" wire:click="$toggle('require_email_verification')" role="switch"
                        class="relative inline-flex h-[22px] w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none
                               {{ $require_email_verification ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-white/[0.12]' }}">
                    <span class="pointer-events-none inline-block h-[18px] w-[18px] rounded-full bg-white shadow transform transition-transform duration-200 ease-in-out
                                 {{ $require_email_verification ? 'translate-x-[18px]' : 'translate-x-0' }}"></span>
                </button>
            </div>

            {{-- Force HTTPS --}}
            <div class="flex items-start justify-between gap-4 p-4 bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/[0.05] rounded-xl">
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Force HTTPS</p>
                    <p class="text-xs text-gray-500 dark:text-white/40 mt-0.5">Redirect all HTTP traffic to HTTPS. Ensure your SSL certificate is valid before enabling.</p>
                </div>
                <button type="button" wire:click="$toggle('force_https')" role="switch"
                        class="relative inline-flex h-[22px] w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none
                               {{ $force_https ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-white/[0.12]' }}">
                    <span class="pointer-events-none inline-block h-[18px] w-[18px] rounded-full bg-white shadow transform transition-transform duration-200 ease-in-out
                                 {{ $force_https ? 'translate-x-[18px]' : 'translate-x-0' }}"></span>
                </button>
            </div>
        </div>

        <div class="flex justify-end mt-6 pt-5 border-t border-gray-100 dark:border-white/[0.06]">
            <button wire:click="saveSecurity" type="button"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 text-black text-sm font-semibold rounded-xl transition-colors">
                <span wire:loading wire:target="saveSecurity" class="w-3.5 h-3.5 border-2 border-black/30 border-t-black rounded-full animate-spin"></span>
                <svg wire:loading.remove wire:target="saveSecurity" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Security Settings
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════  MAINTENANCE CONFIRM MODAL  --}}
    <div x-show="confirmMaintenance"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display:none">
        <div @click.outside="confirmMaintenance = false"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.08] rounded-2xl p-6 w-full max-w-sm shadow-2xl">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-500/10 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-[15px] font-semibold text-gray-900 dark:text-white">Enable Maintenance Mode?</h3>
                    <p class="text-xs text-gray-400 dark:text-white/40 mt-0.5">Visitors will see a maintenance page</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-white/60 mb-5">Your store will be temporarily unavailable to customers. Admin access will continue to work normally.</p>
            <div class="flex items-center gap-3">
                <button @click="confirmMaintenance = false" type="button"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-white/70 bg-gray-100 dark:bg-white/[0.07] hover:bg-gray-200 dark:hover:bg-white/[0.1] rounded-xl transition-colors">
                    Cancel
                </button>
                <button @click="$wire.$toggle('maintenance_mode'); confirmMaintenance = false" type="button"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 active:bg-red-700 rounded-xl transition-colors">
                    Enable Maintenance
                </button>
            </div>
        </div>
    </div>

</div>
