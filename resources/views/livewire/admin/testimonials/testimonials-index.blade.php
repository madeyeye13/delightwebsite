{{--
╔══════════════════════════════════════════════════════════════════╗
║  LIVEWIRE: ADMIN TESTIMONIALS INDEX                               ║
║  Approve, delete, and create testimonials                         ║
╚══════════════════════════════════════════════════════════════════╝
--}}

<div
    class="space-y-6"
    x-data="{ showCreateModal: false, showDeleteModal: false, deleteId: null, tab: '{{ $filterStatus }}' }"
    @close-create-modal.window="showCreateModal = false"
>

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900 dark:text-neutral-50">Testimonials</h1>
            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">Manage customer reviews displayed on the homepage</p>
        </div>
        <button
            @click="showCreateModal = true"
            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors duration-200 font-medium text-sm"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Testimonial
        </button>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Total</p>
            <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Live</p>
            <p class="mt-2 text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['approved'] }}</p>
        </div>
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Pending</p>
            <p class="mt-2 text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</p>
        </div>
    </div>

    {{-- FILTER TABS --}}
    <div class="flex gap-1 bg-neutral-100 dark:bg-neutral-800/50 rounded-lg p-1 w-fit">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Live'] as $key => $label)
            <button
                wire:click="$set('filterStatus', '{{ $key }}')"
                @click="tab = '{{ $key }}'"
                :class="tab === '{{ $key }}' ? 'bg-white dark:bg-neutral-700 text-neutral-900 dark:text-white shadow-sm' : 'text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200'"
                class="px-3 py-1.5 rounded-md text-xs font-medium transition-all duration-150"
            >{{ $label }}</button>
        @endforeach
    </div>

    {{-- TABLE --}}
    <div class="bg-white dark:bg-[#1a2332] rounded-xl border border-neutral-200 dark:border-neutral-800 overflow-hidden">
        @if($testimonials->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-10 h-10 text-neutral-300 dark:text-neutral-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">No testimonials found.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-neutral-200 dark:border-neutral-700">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Customer</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Review</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Rating</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Status</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Source</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Date</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700/50">
                        @foreach($testimonials as $testimonial)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-5 py-4">
                                    <p class="font-medium text-neutral-900 dark:text-neutral-100 text-sm">{{ $testimonial->name }}</p>
                                    @if($testimonial->location)
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">{{ $testimonial->location }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 max-w-xs">
                                    <p class="text-neutral-700 dark:text-neutral-300 text-sm line-clamp-2">{{ $testimonial->quote }}</p>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if($testimonial->rating)
                                        <span class="text-yellow-500 text-sm">{{ str_repeat('★', $testimonial->rating) }}<span class="text-neutral-300 dark:text-neutral-600">{{ str_repeat('★', 5 - $testimonial->rating) }}</span></span>
                                    @else
                                        <span class="text-xs text-neutral-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if($testimonial->is_approved)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Live
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-50 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if($testimonial->is_admin_created)
                                        <span class="text-xs font-medium text-purple-600 dark:text-purple-400">Admin</span>
                                    @else
                                        <span class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Customer</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-xs text-neutral-500 dark:text-neutral-400">
                                    {{ $testimonial->created_at->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        @if($testimonial->is_approved)
                                            <button
                                                wire:click="unapprove({{ $testimonial->id }})"
                                                wire:loading.attr="disabled"
                                                title="Unpublish"
                                                class="px-2.5 py-1 text-xs font-medium rounded-md border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors"
                                            >Unpublish</button>
                                        @else
                                            <button
                                                wire:click="approve({{ $testimonial->id }})"
                                                wire:loading.attr="disabled"
                                                title="Approve"
                                                class="px-2.5 py-1 text-xs font-medium rounded-md bg-emerald-600 text-white hover:bg-emerald-700 transition-colors"
                                            >Approve</button>
                                        @endif
                                        <button
                                            @click="showDeleteModal = true; deleteId = {{ $testimonial->id }}"
                                            title="Delete"
                                            class="px-2.5 py-1 text-xs font-medium rounded-md border border-red-200 dark:border-red-800/50 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                                        >Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ── DELETE CONFIRMATION MODAL ── --}}
    <div
        x-show="showDeleteModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showDeleteModal = false; deleteId = null"></div>

        {{-- Dialog --}}
        <div class="relative bg-white dark:bg-[#1a2332] rounded-xl border border-neutral-200 dark:border-neutral-800 shadow-2xl w-full max-w-sm p-6 z-10">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-500/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-neutral-900 dark:text-neutral-50">Delete Testimonial</h3>
                    <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">This action cannot be undone. The testimonial will be permanently removed.</p>
                </div>
            </div>
            <div class="mt-5 flex gap-3 justify-end">
                <button
                    @click="showDeleteModal = false; deleteId = null"
                    class="px-4 py-2 text-sm font-medium rounded-lg border border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors"
                >Cancel</button>
                <button
                    @click="$wire.delete(deleteId); showDeleteModal = false; deleteId = null"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-70 cursor-not-allowed"
                    wire:target="delete"
                    class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors"
                >
                    <span wire:loading.remove wire:target="delete">Delete</span>
                    <span wire:loading wire:target="delete">Deleting…</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── CREATE TESTIMONIAL MODAL ── --}}
    <div
        x-show="showCreateModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showCreateModal = false"></div>

        {{-- Dialog --}}
        <div class="relative bg-white dark:bg-[#1a2332] rounded-xl border border-neutral-200 dark:border-neutral-800 shadow-2xl w-full max-w-lg p-6 z-10">
            <button
                @click="showCreateModal = false"
                class="absolute top-4 right-4 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 transition-colors"
                type="button"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <h3 class="text-base font-semibold text-neutral-900 dark:text-neutral-50 mb-1">Add Testimonial</h3>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-5">Admin-created testimonials are published immediately.</p>

            {{-- Star Rating --}}
            <div class="mb-4" x-data="{ hovered: 0 }">
                <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-2">Rating (optional)</label>
                <div class="flex gap-1">
                    <template x-for="n in 5" :key="n">
                        <button
                            type="button"
                            class="text-2xl leading-none transition-colors"
                            :class="n <= (hovered || {{ $createRating ?? 0 }}) ? 'text-yellow-400' : 'text-neutral-300 dark:text-neutral-600'"
                            @mouseenter="hovered = n"
                            @mouseleave="hovered = 0"
                            @click="$wire.createRating = n"
                        >★</button>
                    </template>
                    @if($createRating)
                        <button type="button" wire:click="$set('createRating', null)" class="ml-2 text-xs text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 underline">Clear</button>
                    @endif
                </div>
                @error('createRating') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5">Customer Name <span class="text-red-400">*</span></label>
                <input
                    type="text"
                    wire:model="createName"
                    placeholder="e.g. Adaeze Okonkwo"
                    class="w-full px-3 py-2 text-sm rounded-lg border @error('createName') border-red-400 @else border-neutral-300 dark:border-neutral-600 @enderror bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                @error('createName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5">City <span class="text-neutral-400 font-normal">(optional)</span></label>
                <input
                    type="text"
                    wire:model="createLocation"
                    placeholder="e.g. Lagos"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
            </div>

            <div class="mb-6">
                <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5">Review <span class="text-red-400">*</span></label>
                <textarea
                    wire:model="createQuote"
                    placeholder="Write the customer's testimonial here…"
                    rows="4"
                    class="w-full px-3 py-2 text-sm rounded-lg border @error('createQuote') border-red-400 @else border-neutral-300 dark:border-neutral-600 @enderror bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none"
                ></textarea>
                @error('createQuote') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 justify-end">
                <button
                    @click="showCreateModal = false"
                    type="button"
                    class="px-4 py-2 text-sm font-medium rounded-lg border border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors"
                >Cancel</button>
                <button
                    wire:click="create"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-70 cursor-not-allowed"
                    type="button"
                    class="px-4 py-2 text-sm font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition-colors"
                >
                    <span wire:loading.remove wire:target="create">Publish Testimonial</span>
                    <span wire:loading wire:target="create">Publishing…</span>
                </button>
            </div>
            </div>
        </div>
    </div>

</div>
