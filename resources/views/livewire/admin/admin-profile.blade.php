<div class="space-y-6 max-w-2xl">

    {{-- ── PAGE HEADER ── --}}
    <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-600
                    flex items-center justify-center text-white text-xl font-bold shrink-0">
            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
        </div>
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-50">
                {{ auth()->user()->name }}
            </h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5 capitalize">
                {{ auth()->user()->role ?? 'Administrator' }}
                <span class="mx-1.5 text-neutral-300 dark:text-neutral-700">·</span>
                {{ auth()->user()->email }}
            </p>
        </div>
    </div>

    {{-- ── PROFILE INFO ── --}}
    <div class="bg-neutral-50 dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 rounded-lg overflow-hidden">

        <div class="px-5 py-3.5 border-b border-neutral-200 dark:border-neutral-800 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
            </svg>
            <h2 class="text-sm font-semibold text-neutral-900 dark:text-neutral-50">Profile Information</h2>
        </div>

        <div class="px-5 py-5 space-y-4">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-1.5">
                        Full Name <span class="text-red-400">*</span>
                    </label>
                    <input type="text"
                           wire:model="name"
                           class="w-full px-3 py-2 text-sm border rounded
                                  border-neutral-300 dark:border-neutral-700
                                  bg-white dark:bg-neutral-900/50
                                  text-neutral-900 dark:text-neutral-50
                                  placeholder-neutral-400 dark:placeholder-neutral-600
                                  focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent
                                  transition-all"
                           placeholder="Your full name" />
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-1.5">
                        Email Address <span class="text-red-400">*</span>
                    </label>
                    <input type="email"
                           wire:model="email"
                           class="w-full px-3 py-2 text-sm border rounded
                                  border-neutral-300 dark:border-neutral-700
                                  bg-white dark:bg-neutral-900/50
                                  text-neutral-900 dark:text-neutral-50
                                  placeholder-neutral-400 dark:placeholder-neutral-600
                                  focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent
                                  transition-all"
                           placeholder="your@email.com" />
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-1 flex items-center gap-3">
                <button wire:click="saveProfile"
                        wire:loading.attr="disabled"
                        wire:target="saveProfile"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600
                               text-white text-sm font-medium rounded transition-colors
                               disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg wire:loading wire:target="saveProfile" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                    </svg>
                    <svg wire:loading.remove wire:target="saveProfile" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Profile
                </button>
            </div>

        </div>
    </div>

    {{-- ── CHANGE PASSWORD ── --}}
    <div class="bg-neutral-50 dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 rounded-lg overflow-hidden">

        <div class="px-5 py-3.5 border-b border-neutral-200 dark:border-neutral-800 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <h2 class="text-sm font-semibold text-neutral-900 dark:text-neutral-50">Change Password</h2>
        </div>

        <div class="px-5 py-5 space-y-4">

            <div>
                <label class="block text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-1.5">
                    Current Password <span class="text-red-400">*</span>
                </label>
                <input type="password"
                       wire:model="currentPassword"
                       class="w-full px-3 py-2 text-sm border rounded
                              border-neutral-300 dark:border-neutral-700
                              bg-white dark:bg-neutral-900/50
                              text-neutral-900 dark:text-neutral-50
                              placeholder-neutral-400 dark:placeholder-neutral-600
                              focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent
                              transition-all"
                       placeholder="••••••••" />
                @error('currentPassword')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-1.5">
                        New Password <span class="text-red-400">*</span>
                    </label>
                    <input type="password"
                           wire:model="newPassword"
                           class="w-full px-3 py-2 text-sm border rounded
                                  border-neutral-300 dark:border-neutral-700
                                  bg-white dark:bg-neutral-900/50
                                  text-neutral-900 dark:text-neutral-50
                                  placeholder-neutral-400 dark:placeholder-neutral-600
                                  focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent
                                  transition-all"
                           placeholder="Min. 8 characters" />
                    @error('newPassword')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-1.5">
                        Confirm New Password <span class="text-red-400">*</span>
                    </label>
                    <input type="password"
                           wire:model="confirmPassword"
                           class="w-full px-3 py-2 text-sm border rounded
                                  border-neutral-300 dark:border-neutral-700
                                  bg-white dark:bg-neutral-900/50
                                  text-neutral-900 dark:text-neutral-50
                                  placeholder-neutral-400 dark:placeholder-neutral-600
                                  focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent
                                  transition-all"
                           placeholder="Repeat new password" />
                    @error('confirmPassword')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="p-3 rounded bg-amber-50 dark:bg-amber-500/[0.06] border border-amber-200 dark:border-amber-500/20 flex items-start gap-2">
                <svg class="w-3.5 h-3.5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs text-amber-700 dark:text-amber-400">
                    Use at least 8 characters including a mix of letters and numbers.
                </p>
            </div>

            <div class="pt-1 flex items-center gap-3">
                <button wire:click="changePassword"
                        wire:loading.attr="disabled"
                        wire:target="changePassword"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-neutral-800 hover:bg-neutral-700
                               dark:bg-neutral-700 dark:hover:bg-neutral-600
                               text-white text-sm font-medium rounded transition-colors
                               disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg wire:loading wire:target="changePassword" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                    </svg>
                    <svg wire:loading.remove wire:target="changePassword" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.957-5.957A6 6 0 1121 9z"/>
                    </svg>
                    Update Password
                </button>
            </div>

        </div>
    </div>

    {{-- ── ACCOUNT META ── --}}
    <div class="bg-neutral-50 dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 rounded-lg overflow-hidden">

        <div class="px-5 py-3.5 border-b border-neutral-200 dark:border-neutral-800 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <h2 class="text-sm font-semibold text-neutral-900 dark:text-neutral-50">Account Details</h2>
        </div>

        <div class="px-5 py-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <p class="text-xs text-neutral-400 dark:text-neutral-500 mb-1">Role</p>
                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold
                             bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    {{ ucfirst(auth()->user()->role ?? 'admin') }}
                </span>
            </div>
            <div>
                <p class="text-xs text-neutral-400 dark:text-neutral-500 mb-1">Member Since</p>
                <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">
                    {{ auth()->user()->created_at->format('d M Y') }}
                </p>
            </div>
            <div>
                <p class="text-xs text-neutral-400 dark:text-neutral-500 mb-1">Status</p>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-semibold
                             bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                    Active
                </span>
            </div>
        </div>

    </div>

</div>
