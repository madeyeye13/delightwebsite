<div class="space-y-6">

    {{-- ════════════════════════════════════════════════════════════
         FLASH NOTIFICATION
    ════════════════════════════════════════════════════════════════ --}}
    <div
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:notify.window="message = $event.detail.message; type = $event.detail.type; show = true; setTimeout(() => show = false, 3500)"
        x-show="show"
        x-transition
        class="fixed top-5 right-5 z-[100] flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg border text-sm font-medium"
        :class="type === 'success' ? 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/30 dark:border-green-700/50 dark:text-green-300' : 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/30 dark:border-red-700/50 dark:text-red-300'"
        style="display:none"
    >
        <span x-text="message"></span>
        <button @click="show = false" class="ml-2 opacity-60 hover:opacity-100">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         CREATE / EDIT USER MODAL
         x-data wrapper + x-show = instant open without server round-trip
    ════════════════════════════════════════════════════════════════ --}}
    <div
        x-data="{ show: false }"
        x-on:open-user-modal.window="show = true"
        x-on:close-user-modal.window="show = false"
    >
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4"
        style="display:none;backdrop-filter:blur(2px)"
        @click.self="show = false"
    >
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-2xl w-full max-w-md"
             @click.stop>
            <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                <div>
                    <h2 class="font-semibold text-sm text-neutral-900 dark:text-neutral-50">
                        {{ $editUserId ? 'Edit User' : 'Create User' }}
                    </h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                        {{ $editUserId ? 'Update user details below.' : 'Fill in the details to create a new user.' }}
                    </p>
                </div>
                <button @click="show = false" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors text-neutral-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="px-5 py-4 space-y-4">
                {{-- Name --}}
                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="modalName" placeholder="Full name"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 placeholder-neutral-400 focus:ring-2 focus:ring-brand focus:border-transparent transition-all" />
                    @error('modalName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" wire:model="modalEmail" placeholder="email@example.com"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 placeholder-neutral-400 focus:ring-2 focus:ring-brand focus:border-transparent transition-all" />
                    @error('modalEmail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Role --}}
                <div x-data="{ open: false }">
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Role <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <button type="button" @click="open = !open" @click.away="open = false"
                                class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded text-left text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 flex items-center justify-between hover:border-neutral-400 transition-colors">
                            <span class="capitalize">{{ $modalRole ?: 'Select role' }}</span>
                            <svg class="w-3 h-3 text-neutral-400" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-10">
                            @foreach(['customer' => 'Customer', 'staff' => 'Staff', 'admin' => 'Admin'] as $value => $label)
                                <button type="button" @click="open = false" wire:click="$set('modalRole', '{{ $value }}')"
                                        class="w-full text-left px-3 py-2 text-sm text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 {{ $modalRole === $value ? 'font-semibold text-brand' : '' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @error('modalRole') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                        Password {{ $editUserId ? '<span class="text-neutral-400 font-normal">(leave blank to keep current)</span>' : '<span class="text-red-500">*</span>' }}
                    </label>
                    <input type="password" wire:model="modalPassword" placeholder="Min. 8 characters"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 placeholder-neutral-400 focus:ring-2 focus:ring-brand focus:border-transparent transition-all" />
                    @error('modalPassword') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Confirm Password</label>
                    <input type="password" wire:model="modalPasswordConfirm" placeholder="Repeat password"
                           class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 placeholder-neutral-400 focus:ring-2 focus:ring-brand focus:border-transparent transition-all" />
                    @error('modalPasswordConfirm') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Active toggle --}}
                <div class="flex items-center justify-between py-1">
                    <div>
                        <p class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Active</p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Inactive users cannot log in to the admin.</p>
                    </div>
                    <button type="button" wire:click="$set('modalIsActive', {{ $modalIsActive ? 'false' : 'true' }})"
                            class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors {{ $modalIsActive ? 'bg-brand' : 'bg-neutral-300 dark:bg-neutral-600' }}">
                        <span class="inline-block w-3.5 h-3.5 transform rounded-full bg-white shadow transition-transform {{ $modalIsActive ? 'translate-x-[18px]' : 'translate-x-[3px]' }}"></span>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-neutral-200 dark:border-neutral-700">
                <button @click="show = false" class="px-4 py-2 text-xs font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors">Cancel</button>
                <button wire:click="saveUser" class="px-4 py-2 text-xs font-medium bg-brand text-white rounded-lg hover:bg-brand-600 transition-colors" wire:loading.attr="disabled" wire:target="saveUser">
                    <span wire:loading.remove wire:target="saveUser">{{ $editUserId ? 'Update User' : 'Create User' }}</span>
                    <span wire:loading wire:target="saveUser">Saving…</span>
                </button>
            </div>
        </div>
    </div>
    </div>{{-- /x-data user-modal --}}

    {{-- ════════════════════════════════════════════════════════════
         PERMISSIONS MODAL
    ════════════════════════════════════════════════════════════════ --}}
    <div
        x-data="{ show: false }"
        x-on:open-permissions-modal.window="show = true"
        x-on:close-permissions-modal.window="show = false"
    >
    @php $permUser = $permissionsUserId ? \App\Models\User::find($permissionsUserId) : null; @endphp
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4"
        style="display:none;backdrop-filter:blur(2px)"
        @click.self="show = false"
    >
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-2xl w-full max-w-sm"
             @click.stop>
            <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                <div>
                    <h2 class="font-semibold text-sm text-neutral-900 dark:text-neutral-50">Staff Permissions</h2>
                    @if($permUser)
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">{{ $permUser->name }}</p>
                    @endif
                </div>
                <button @click="show = false" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors text-neutral-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="px-5 py-4">
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-3">Select which admin pages this staff member can access.</p>
                <div class="space-y-2">
                    @foreach($adminPages as $key => $label)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" wire:model="permissionsSelected" value="{{ $key }}"
                                   class="w-4 h-4 rounded border-neutral-300 dark:border-neutral-600 text-brand bg-white dark:bg-neutral-900 focus:ring-brand focus:ring-offset-0" />
                            <span class="text-sm text-neutral-700 dark:text-neutral-300 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-neutral-200 dark:border-neutral-700">
                <button @click="show = false" class="px-4 py-2 text-xs font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors">Cancel</button>
                <button wire:click="savePermissions" class="px-4 py-2 text-xs font-medium bg-brand text-white rounded-lg hover:bg-brand-600 transition-colors" wire:loading.attr="disabled" wire:target="savePermissions">
                    <span wire:loading.remove wire:target="savePermissions">Save Permissions</span>
                    <span wire:loading wire:target="savePermissions">Saving…</span>
                </button>
            </div>
        </div>
    </div>
    </div>{{-- /x-data permissions-modal --}}

    {{-- ════════════════════════════════════════════════════════════
         DELETE CONFIRM MODAL
    ════════════════════════════════════════════════════════════════ --}}
    <div
        x-data="{ show: false }"
        x-on:open-delete-confirm.window="show = true"
        x-on:close-delete-confirm.window="show = false"
    >
    @php $deleteUser = $deleteUserId ? \App\Models\User::find($deleteUserId) : null; @endphp
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4"
        style="display:none;backdrop-filter:blur(2px)"
        @click.self="show = false"
    >
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-2xl w-full max-w-sm"
             @click.stop>
            <div class="p-5 text-center">
                <div class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-500/10 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-neutral-900 dark:text-neutral-50">Delete User?</h3>
                @if($deleteUser)
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                        This will permanently delete <strong class="text-neutral-700 dark:text-neutral-300">{{ $deleteUser->name }}</strong>. This action cannot be undone.
                    </p>
                @endif
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-neutral-200 dark:border-neutral-700">
                <button @click="show = false" class="px-4 py-2 text-xs font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors">Cancel</button>
                <button wire:click="deleteUser" class="px-4 py-2 text-xs font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors" wire:loading.attr="disabled" wire:target="deleteUser">
                    <span wire:loading.remove wire:target="deleteUser">Delete</span>
                    <span wire:loading wire:target="deleteUser">Deleting…</span>
                </button>
            </div>
        </div>
    </div>
    </div>{{-- /x-data delete-confirm --}}

    {{-- ════════════════════════════════════════════════════════════
         SIDE PANEL
    ════════════════════════════════════════════════════════════════ --}}
    @if($showSidePanel && $this->sidePanelData)
    @php $panelData = $this->sidePanelData; $panelUser = $panelData['user']; @endphp
    <div class="fixed inset-0 z-[9998] flex justify-end" wire:click.self="closeSidePanel">
        <div class="absolute inset-0 bg-black/40" wire:click="closeSidePanel"></div>
        <div class="relative w-full max-w-sm bg-neutral-50 dark:bg-[#1a2332] border-l border-neutral-200 dark:border-neutral-700 h-full overflow-y-auto flex flex-col shadow-2xl">
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-200 dark:border-neutral-700 sticky top-0 bg-neutral-50 dark:bg-[#1a2332]">
                <h3 class="font-semibold text-sm text-neutral-900 dark:text-neutral-50">User Details</h3>
                <button wire:click="closeSidePanel" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors text-neutral-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="flex-1 p-5 space-y-5">
                {{-- Avatar + name --}}
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-brand/70 to-brand flex items-center justify-center text-white text-xl font-bold shadow-sm shrink-0">
                        {{ strtoupper(substr($panelUser->name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="font-semibold text-base text-neutral-900 dark:text-neutral-50">{{ $panelUser->name }}</h4>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $panelUser->email }}</p>
                        <div class="mt-1 flex items-center gap-1.5">
                            @php
                                $roleClass = match($panelUser->role) {
                                    'admin'  => 'bg-purple-50 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300',
                                    'staff'  => 'bg-blue-50 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300',
                                    default  => 'bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $roleClass }} capitalize">{{ $panelUser->role }}</span>
                            @if(!$panelUser->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-red-50 dark:bg-red-500/20 text-red-700 dark:text-red-300">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Order stats --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-neutral-100 dark:bg-neutral-900/60 rounded-lg p-3">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-neutral-500">Orders</p>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-50 mt-1">{{ number_format($panelData['order_count']) }}</p>
                    </div>
                    <div class="bg-neutral-100 dark:bg-neutral-900/60 rounded-lg p-3">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-neutral-500">Total Spent</p>
                        <p class="text-lg font-bold text-neutral-900 dark:text-neutral-50 mt-1">₦{{ number_format($panelData['total_spent']) }}</p>
                    </div>
                </div>

                {{-- Detail rows --}}
                <dl class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <dt class="text-neutral-500 dark:text-neutral-400">Joined</dt>
                        <dd class="font-medium text-neutral-900 dark:text-neutral-50 text-xs">{{ $panelUser->created_at->format('d M Y') }}</dd>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <dt class="text-neutral-500 dark:text-neutral-400">Last Order</dt>
                        <dd class="font-medium text-neutral-900 dark:text-neutral-50 text-xs">{{ $panelData['last_order'] }}</dd>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <dt class="text-neutral-500 dark:text-neutral-400">Email Verified</dt>
                        <dd class="text-xs">
                            @if($panelUser->email_verified_at)
                                <span class="text-green-600 dark:text-green-400 font-medium">Yes</span>
                            @else
                                <span class="text-neutral-400">No</span>
                            @endif
                        </dd>
                    </div>
                    @if($panelUser->role === 'staff')
                    <div class="flex items-start justify-between text-sm">
                        <dt class="text-neutral-500 dark:text-neutral-400 mt-0.5">Permissions</dt>
                        <dd class="text-xs text-right max-w-[150px]">
                            @if(!empty($panelUser->permissions))
                                <div class="flex flex-wrap justify-end gap-1">
                                    @foreach($panelUser->permissions as $perm)
                                        <span class="inline-block px-1.5 py-0.5 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded text-[10px] font-medium capitalize">{{ $perm }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-neutral-400">None set</span>
                            @endif
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Footer actions --}}
            <div class="p-5 border-t border-neutral-200 dark:border-neutral-700 space-y-2 sticky bottom-0 bg-neutral-50 dark:bg-[#1a2332]">
                <button @click="$dispatch('open-user-modal')" wire:click="openEditModal({{ $panelUser->id }})"
                        class="w-full px-4 py-2.5 bg-brand text-white text-xs font-semibold rounded-lg hover:bg-brand-600 transition-colors text-center">
                    Edit User
                </button>
                @if($panelUser->role === 'staff')
                <button @click="$dispatch('open-permissions-modal')" wire:click="openPermissionsModal({{ $panelUser->id }})"
                        class="w-full px-4 py-2.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors text-center">
                    Manage Permissions
                </button>
                @endif
                @if($panelUser->id !== auth()->id())
                <button wire:click="toggleActive({{ $panelUser->id }})"
                        class="w-full px-4 py-2.5 text-xs font-semibold rounded-lg transition-colors text-center
                               {{ $panelUser->is_active ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 hover:bg-amber-200 dark:hover:bg-amber-500/30' : 'bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-500/30' }}">
                    {{ $panelUser->is_active ? 'Deactivate Account' : 'Activate Account' }}
                </button>
                <button @click="$dispatch('open-delete-confirm')" wire:click="confirmDelete({{ $panelUser->id }})"
                        class="w-full px-4 py-2.5 bg-neutral-100 dark:bg-neutral-800 text-red-600 dark:text-red-400 text-xs font-semibold rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors text-center">
                    Delete User
                </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════
         STATS CARDS
    ════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Total Users</p>
            <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-neutral-50">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Customers</p>
            <p class="mt-2 text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['customer']) }}</p>
        </div>
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Admins</p>
            <p class="mt-2 text-3xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($stats['admin']) }}</p>
        </div>
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Staff</p>
            <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($stats['staff']) }}</p>
        </div>
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Inactive</p>
            <p class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">{{ number_format($stats['inactive']) }}</p>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         FILTERS & SEARCH
    ════════════════════════════════════════════════════════════════ --}}
    <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
        {{-- Search + Create --}}
        <div class="flex items-center gap-3 mb-4">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by name or email…"
                class="flex-1 px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 placeholder-neutral-500 dark:placeholder-neutral-500 focus:ring-2 focus:ring-brand focus:border-transparent transition-all"
            />
            <button @click="$dispatch('open-user-modal')" wire:click="openCreateModal"
                    class="flex items-center gap-1.5 px-3 py-2 bg-brand text-white text-xs font-semibold rounded hover:bg-brand-600 transition-colors whitespace-nowrap">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New User
            </button>
        </div>

        {{-- Filter Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5">

            {{-- Role filter --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Role</label>
                <div class="relative">
                    <button @click="open = !open" @click.away="open = false"
                            class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 dark:hover:border-neutral-600 flex items-center justify-between transition-colors">
                        <span>{{ $roleFilter ? ucfirst($roleFilter) : 'All Roles' }}</span>
                        <svg class="w-3 h-3 text-neutral-400 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20">
                        @foreach(['' => 'All Roles', 'admin' => 'Admin', 'staff' => 'Staff', 'customer' => 'Customer'] as $val => $label)
                            <button @click="open = false" wire:click="$set('roleFilter', '{{ $val }}')"
                                    class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 {{ $roleFilter === $val ? 'font-semibold text-brand' : '' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Status filter --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Status</label>
                <div class="relative">
                    <button @click="open = !open" @click.away="open = false"
                            class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 dark:hover:border-neutral-600 flex items-center justify-between transition-colors">
                        <span>{{ match($statusFilter) { 'active' => 'Active', 'inactive' => 'Inactive', default => 'All Status' } }}</span>
                        <svg class="w-3 h-3 text-neutral-400 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20">
                        @foreach(['' => 'All Status', 'active' => 'Active', 'inactive' => 'Inactive'] as $val => $label)
                            <button @click="open = false" wire:click="$set('statusFilter', '{{ $val }}')"
                                    class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 {{ $statusFilter === $val ? 'font-semibold text-brand' : '' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sort --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Sort</label>
                <div class="relative">
                    <button @click="open = !open" @click.away="open = false"
                            class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 dark:hover:border-neutral-600 flex items-center justify-between transition-colors">
                        <span>{{ match($sortBy) { 'oldest' => 'Oldest First', 'name-asc' => 'Name A→Z', 'name-desc' => 'Name Z→A', default => 'Newest First' } }}</span>
                        <svg class="w-3 h-3 text-neutral-400 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20">
                        @foreach(['newest' => 'Newest First', 'oldest' => 'Oldest First', 'name-asc' => 'Name A→Z', 'name-desc' => 'Name Z→A'] as $val => $label)
                            <button @click="open = false" wire:click="$set('sortBy', '{{ $val }}')"
                                    class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 {{ $sortBy === $val ? 'font-semibold text-brand' : '' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Per page --}}
            <div>
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Per Page</label>
                <select wire:model.live="perPage"
                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 focus:ring-1 focus:ring-brand transition-colors">
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            {{-- Clear + Export --}}
            <div class="flex items-end gap-2">
                <button wire:click="clearFilters" class="text-brand dark:text-brand-300 hover:text-brand-600 font-medium text-xs whitespace-nowrap transition-colors">
                    Clear
                </button>
                <button wire:click="exportUsers"
                        class="ml-auto px-2.5 py-1.5 text-xs font-medium bg-neutral-200 dark:bg-neutral-700 text-neutral-900 dark:text-neutral-50 rounded hover:bg-neutral-300 dark:hover:bg-neutral-600 transition-colors whitespace-nowrap"
                        wire:loading.attr="disabled" wire:target="exportUsers">
                    <span wire:loading.remove wire:target="exportUsers">Export CSV</span>
                    <span wire:loading wire:target="exportUsers">Exporting…</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         USERS TABLE
    ════════════════════════════════════════════════════════════════ --}}
    <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 overflow-hidden">

        {{-- Footer info bar --}}
        @if(!$users->isEmpty())
        <div class="flex items-center justify-between px-4 py-2.5 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-100/70 dark:bg-neutral-900/60">
            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                Showing <span class="font-semibold text-neutral-700 dark:text-neutral-300">{{ $users->firstItem() }}</span>–<span class="font-semibold text-neutral-700 dark:text-neutral-300">{{ $users->lastItem() }}</span> of <span class="font-semibold text-neutral-700 dark:text-neutral-300">{{ $users->total() }}</span> users
            </p>
            <span class="text-xs text-neutral-400 dark:text-neutral-600">Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</span>
        </div>
        @endif

        {{-- Loading overlay --}}
        <div wire:loading wire:target="search,roleFilter,statusFilter,sortBy,perPage"
             class="absolute inset-0 bg-white/50 dark:bg-black/30 z-10 flex items-center justify-center rounded-lg">
            <svg class="w-6 h-6 animate-spin text-brand" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-dasharray="31.4 31.4" stroke-linecap="round"/></svg>
        </div>

        {{-- Empty state --}}
        @if($users->isEmpty())
        <div class="p-12 text-center">
            <svg class="w-12 h-12 text-neutral-300 dark:text-neutral-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm9 0v6m3-3h-6"/>
            </svg>
            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">No users found</p>
            <p class="text-xs text-neutral-400 dark:text-neutral-600 mt-1">Try adjusting your filters or search query.</p>
            @if($search || $roleFilter || $statusFilter)
            <button wire:click="clearFilters" class="mt-3 text-xs text-brand hover:text-brand-600 font-medium transition-colors">Clear filters</button>
            @endif
        </div>
        @else

        {{-- Desktop table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-200 dark:border-neutral-800">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">User</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Role</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Joined</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @foreach($users as $user)
                    @php
                        $isCurrentUser = $user->id === auth()->id();
                        $roleClass = match($user->role) {
                            'admin'  => 'bg-purple-50 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300',
                            'staff'  => 'bg-blue-50 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300',
                            default  => 'bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300',
                        };
                    @endphp
                    <tr class="hover:bg-neutral-100/50 dark:hover:bg-neutral-900/30 transition-colors group">
                        {{-- User info --}}
                        <td class="px-4 py-3">
                            <button wire:click="openSidePanel({{ $user->id }})" class="flex items-center gap-3 text-left w-full">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand/60 to-brand flex items-center justify-center text-white text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-neutral-900 dark:text-neutral-50 text-sm truncate">
                                        {{ $user->name }}
                                        @if($isCurrentUser)
                                            <span class="ml-1 text-[10px] text-neutral-400">(you)</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate">{{ $user->email }}</p>
                                </div>
                            </button>
                        </td>

                        {{-- Role --}}
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $roleClass }} capitalize">{{ $user->role }}</span>
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            @if($user->is_active)
                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-green-600 dark:text-green-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-red-500 dark:text-red-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>Inactive
                                </span>
                            @endif
                        </td>

                        {{-- Joined --}}
                        <td class="px-4 py-3 text-xs text-neutral-500 dark:text-neutral-400">{{ $user->created_at->format('d M Y') }}</td>

                        {{-- Actions --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="openSidePanel({{ $user->id }})"
                                        title="View details"
                                        class="w-7 h-7 flex items-center justify-center rounded hover:bg-neutral-200 dark:hover:bg-neutral-700 text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <button @click="$dispatch('open-user-modal')" wire:click="openEditModal({{ $user->id }})"
                                        title="Edit"
                                        class="w-7 h-7 flex items-center justify-center rounded hover:bg-neutral-200 dark:hover:bg-neutral-700 text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                @if($user->role === 'staff')
                                <button @click="$dispatch('open-permissions-modal')" wire:click="openPermissionsModal({{ $user->id }})"
                                        title="Permissions"
                                        class="w-7 h-7 flex items-center justify-center rounded hover:bg-blue-100 dark:hover:bg-blue-500/20 text-neutral-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </button>
                                @endif
                                @if(!$isCurrentUser)
                                <button @click="$dispatch('open-delete-confirm')" wire:click="confirmDelete({{ $user->id }})"
                                        title="Delete"
                                        class="w-7 h-7 flex items-center justify-center rounded hover:bg-red-100 dark:hover:bg-red-500/20 text-neutral-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="md:hidden divide-y divide-neutral-100 dark:divide-neutral-800">
            @foreach($users as $user)
            @php
                $roleClass = match($user->role) {
                    'admin'  => 'bg-purple-50 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300',
                    'staff'  => 'bg-blue-50 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300',
                    default  => 'bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300',
                };
            @endphp
            <div class="p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <button wire:click="openSidePanel({{ $user->id }})" class="flex items-center gap-3 text-left">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-brand/60 to-brand flex items-center justify-center text-white text-sm font-bold shrink-0">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-neutral-900 dark:text-neutral-50 text-sm">{{ $user->name }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $user->email }}</p>
                        </div>
                    </button>
                    <div class="flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $roleClass }} capitalize">{{ $user->role }}</span>
                        @if(!$user->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-red-50 dark:bg-red-500/20 text-red-600 dark:text-red-400">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-between text-xs text-neutral-500 dark:text-neutral-400">
                    <span>Joined {{ $user->created_at->format('d M Y') }}</span>
                    <div class="flex items-center gap-1">
                        <button @click="$dispatch('open-user-modal')" wire:click="openEditModal({{ $user->id }})" class="px-2 py-1 text-xs text-brand font-medium hover:underline">Edit</button>
                        @if($user->id !== auth()->id())
                        <button @click="$dispatch('open-delete-confirm')" wire:click="confirmDelete({{ $user->id }})" class="px-2 py-1 text-xs text-red-500 font-medium hover:underline">Delete</button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="flex items-center justify-between px-4 py-3 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-100/70 dark:bg-neutral-900/60">
            <button
                wire:click="previousPage"
                @disabled($users->onFirstPage())
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded transition-colors
                       {{ $users->onFirstPage() ? 'text-neutral-300 dark:text-neutral-700 cursor-not-allowed' : 'text-neutral-700 dark:text-neutral-300 hover:bg-neutral-200 dark:hover:bg-neutral-700' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Prev
            </button>
            <span class="text-xs text-neutral-500 dark:text-neutral-400">
                Page {{ $users->currentPage() }} / {{ $users->lastPage() }}
            </span>
            <button
                wire:click="nextPage"
                @disabled(!$users->hasMorePages())
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded transition-colors
                       {{ !$users->hasMorePages() ? 'text-neutral-300 dark:text-neutral-700 cursor-not-allowed' : 'text-neutral-700 dark:text-neutral-300 hover:bg-neutral-200 dark:hover:bg-neutral-700' }}">
                Next
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
        @endif

        @endif
    </div>
</div>
