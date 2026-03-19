<div class="p-6 md:p-8 max-w-2xl mx-auto"
     x-data="{}"
     @profile-saved.window="$dispatch('show-toast', { msg: 'Profile updated.', type: 'success' })"
     @password-changed.window="$dispatch('show-toast', { msg: 'Password changed successfully.', type: 'success' })">

    <div class="mb-8">
        <h1 class="font-display text-2xl font-semibold text-white tracking-tight">Profile</h1>
        <p class="text-sm text-white/40 mt-1">Manage your personal information</p>
    </div>

    {{-- ── Profile Info ── --}}
    <div class="bg-white/[0.03] border border-white/[0.07] rounded-2xl p-5 mb-4">
        <h2 class="text-xs font-semibold text-white/40 tracking-widest uppercase mb-5">Personal Details</h2>

        <div class="space-y-4">
            <div>
                <label class="block text-[11px] font-medium tracking-widests uppercase text-white/35 mb-1.5">Full Name</label>
                <input wire:model="name" type="text"
                       class="w-full bg-white/[0.04] border border-white/[0.08] rounded-xl
                              text-sm text-white placeholder-white/20 px-4 py-2.5
                              focus:outline-none focus:border-brand-500/50 transition-colors" />
                @error('name')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-[11px] font-medium tracking-widests uppercase text-white/35 mb-1.5">Email Address</label>
                <input wire:model="email" type="email"
                       class="w-full bg-white/[0.04] border border-white/[0.08] rounded-xl
                              text-sm text-white placeholder-white/20 px-4 py-2.5
                              focus:outline-none focus:border-brand-500/50 transition-colors" />
                @error('email')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mt-5">
            <button wire:click="saveProfile" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                           bg-brand-500/10 hover:bg-brand-500/20 border border-brand-500/30
                           text-brand-400 hover:text-brand-300 text-sm font-medium
                           transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg wire:loading wire:target="saveProfile" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                </svg>
                <span wire:loading.remove wire:target="saveProfile">Save Changes</span>
                <span wire:loading wire:target="saveProfile">Saving…</span>
            </button>
        </div>
    </div>

    {{-- ── Change Password ── --}}
    <div class="bg-white/[0.03] border border-white/[0.07] rounded-2xl p-5">
        <h2 class="text-xs font-semibold text-white/40 tracking-widests uppercase mb-5">Change Password</h2>

        <div class="space-y-4">
            <div>
                <label class="block text-[11px] font-medium tracking-widests uppercase text-white/35 mb-1.5">Current Password</label>
                <input wire:model="currentPassword" type="password"
                       class="w-full bg-white/[0.04] border border-white/[0.08] rounded-xl
                              text-sm text-white placeholder-white/20 px-4 py-2.5
                              focus:outline-none focus:border-brand-500/50 transition-colors" />
                @error('currentPassword')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-[11px] font-medium tracking-widests uppercase text-white/35 mb-1.5">New Password</label>
                <input wire:model="newPassword" type="password"
                       class="w-full bg-white/[0.04] border border-white/[0.08] rounded-xl
                              text-sm text-white placeholder-white/20 px-4 py-2.5
                              focus:outline-none focus:border-brand-500/50 transition-colors" />
                @error('newPassword')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-[11px] font-medium tracking-widests uppercase text-white/35 mb-1.5">Confirm New Password</label>
                <input wire:model="confirmPassword" type="password"
                       class="w-full bg-white/[0.04] border border-white/[0.08] rounded-xl
                              text-sm text-white placeholder-white/20 px-4 py-2.5
                              focus:outline-none focus:border-brand-500/50 transition-colors" />
                @error('confirmPassword')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mt-5">
            <button wire:click="changePassword" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                           bg-white/[0.04] hover:bg-white/[0.07] border border-white/[0.08]
                           text-white/60 hover:text-white text-sm font-medium
                           transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg wire:loading wire:target="changePassword" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                </svg>
                <span wire:loading.remove wire:target="changePassword">Update Password</span>
                <span wire:loading wire:target="changePassword">Updating…</span>
            </button>
        </div>
    </div>

</div>