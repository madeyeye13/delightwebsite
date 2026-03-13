{{--
    Reusable Media Picker Modal
    File: resources/views/admin/partials/media-picker.blade.php

    USAGE — include anywhere you need a media picker:
    ─────────────────────────────────────────────────
    @include('admin.partials.media-picker')

    Then trigger it from any element:
        @click="$dispatch('open-media-picker', { mode: 'single', target: 'mainImage' })"
        @click="$dispatch('open-media-picker', { mode: 'multiple', target: 'gallery' })"

    Listen for the selection result:
        @media-selected.window="handleMediaSelected($event.detail)"

    Event detail shape:
        { target: 'mainImage', items: [ mediaItem, ... ] }
        // single mode  → items always has exactly 1 item
        // multiple mode → items has 1+ items

    @livewire: When migrating, replace the allItems mock array with
        a Livewire property $pickerMedia loaded via mount() or a
        computed property. Bind filters with wire:model.
    ─────────────────────────────────────────────────
--}}

<div
    x-data="adminMediaPicker()"
    x-init="init()"
    @open-media-picker.window="open($event.detail)"
    @keydown.escape.window="isOpen && close()"
>
    {{-- ═══════════════════════════════
         MODAL BACKDROP + CONTAINER
    ═══════════════════════════════ --}}
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
        @click.self="close()"
        style="display:none"
    >
        <div
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.08] rounded-xl w-full max-w-5xl overflow-hidden flex flex-col"
            style="height: 80vh; display:none"
        >

            {{-- ── HEADER ────────────────────────── --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-white/[0.06] shrink-0">
                <div>
                    <h2 class="text-[13px] font-semibold text-gray-900 dark:text-white">
                        <span x-text="mode === 'single' ? 'Select Media' : 'Select Media'"></span>
                    </h2>
                    <p class="text-[11px] text-gray-400 dark:text-white/30 mt-0.5">
                        <span x-show="mode === 'single'">Click any file to select it</span>
                        <span x-show="mode === 'multiple'">Click files to select, then confirm</span>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    {{-- Upload from picker --}}
                    <button
                        @click="$dispatch('open-upload-trigger')"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-[12px] font-medium rounded-lg border border-gray-200 dark:border-white/[0.08] text-gray-600 dark:text-white/50 hover:border-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-400 transition-all"
                    >
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/>
                            <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                        </svg>
                        Upload
                    </button>
                    <button
                        @click="close()"
                        class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 dark:text-white/30 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-all"
                    >
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- ── TOOLBAR ───────────────────────── --}}
            <div class="flex items-center gap-3 px-5 py-2.5 border-b border-gray-100 dark:border-white/[0.06] bg-gray-50 dark:bg-white/[0.02] shrink-0 flex-wrap">
                {{-- Search --}}
                <div class="relative flex-1 min-w-36 max-w-56">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 dark:text-white/25 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    {{-- @livewire: wire:model.debounce.300ms="pickerSearch" --}}
                    <input
                        type="text"
                        x-model="pfilters.search"
                        @input.debounce.200ms="applyFilters()"
                        placeholder="Search files..."
                        class="w-full pl-9 pr-3 py-1.5 text-[12px] rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-800 dark:text-white/70 placeholder-gray-300 dark:placeholder-white/25 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    >
                </div>

                {{-- Folder dropdown --}}
                {{-- @livewire: wire:model="pickerFolder" --}}
                <select
                    x-model="pfilters.folder"
                    @change="applyFilters()"
                    class="px-3 py-1.5 text-[12px] rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-600 dark:text-white/50 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                >
                    <template x-for="f in folders" :key="f.id">
                        <option :value="f.id" x-text="f.name"></option>
                    </template>
                </select>

                {{-- Type pills --}}
                <div class="flex items-center gap-1">
                    <template x-for="t in typeFilters" :key="t.value">
                        <button
                            @click="pfilters.type = t.value; applyFilters()"
                            :class="pfilters.type === t.value
                                ? 'bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 border-emerald-400/30'
                                : 'text-gray-500 dark:text-white/40 border-gray-200 dark:border-white/[0.08] hover:text-gray-700 dark:hover:text-white/60'"
                            class="px-2.5 py-1 text-[12px] font-medium rounded-lg border transition-all"
                            x-text="t.label"
                        ></button>
                    </template>
                </div>

                <div class="flex-1"></div>

                {{-- Selected count badge --}}
                <template x-if="pickerSelected.length > 0">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[11px] font-semibold rounded-full">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <span x-text="pickerSelected.length + (mode === 'single' ? ' selected' : ' selected')"></span>
                    </span>
                </template>
            </div>

            {{-- ── BODY ──────────────────────────── --}}
            <div class="flex flex-1 min-h-0 overflow-hidden">

                {{-- Left: folder sidebar (condensed) --}}
                <aside class="hidden md:flex flex-col w-40 shrink-0 border-r border-gray-100 dark:border-white/[0.06] overflow-y-auto custom-scrollbar">
                    <nav class="px-2 py-2 space-y-0.5">
                        <template x-for="folder in folders.filter(f => !f.parent_id)" :key="folder.id">
                            <div>
                                <button
                                    @click="pfilters.folder = folder.id; applyFilters(); folder._expanded = !folder._expanded"
                                    :class="[
                                        'w-full flex items-center gap-2 px-2 py-1.5 rounded-lg text-left transition-all text-[12px] font-medium',
                                        pfilters.folder === folder.id
                                            ? 'bg-emerald-500/10 text-emerald-500 dark:text-emerald-400'
                                            : 'text-gray-500 dark:text-white/40 hover:bg-gray-50 dark:hover:bg-white/[0.03] hover:text-gray-800 dark:hover:text-white/70'
                                    ]"
                                >
                                    <template x-if="folder.id === 'all'">
                                        <svg class="w-3 h-3 shrink-0 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                                    </template>
                                    <template x-if="folder.id !== 'all'">
                                        <svg class="w-3 h-3 shrink-0 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                    </template>
                                    <span class="flex-1 truncate" x-text="folder.name"></span>
                                    <span class="text-[9px] opacity-50 tabular-nums" x-text="folder.count"></span>
                                    <template x-if="folders.some(f => f.parent_id === folder.id)">
                                        <svg class="w-2.5 h-2.5 shrink-0 transition-transform" :class="folder._expanded ? 'rotate-90' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                                    </template>
                                </button>
                                {{-- Nested subfolders --}}
                                <template x-if="folders.some(f => f.parent_id === folder.id) && folder._expanded">
                                    <div class="pl-3 space-y-0.5 mt-0.5 border-l border-gray-200 dark:border-white/[0.08] ml-2">
                                        <template x-for="subfolder in folders.filter(f => f.parent_id === folder.id)" :key="subfolder.id">
                                            <button
                                                @click="pfilters.folder = subfolder.id; applyFilters()"
                                                :class="[
                                                    'w-full flex items-center gap-2 px-2 py-1.5 rounded-lg text-left transition-all text-[12px] font-medium',
                                                    pfilters.folder === subfolder.id
                                                        ? 'bg-emerald-500/10 text-emerald-500 dark:text-emerald-400'
                                                        : 'text-gray-500 dark:text-white/40 hover:bg-gray-50 dark:hover:bg-white/[0.03] hover:text-gray-800 dark:hover:text-white/70'
                                                ]"
                                            >
                                                <svg class="w-3 h-3 shrink-0 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                                <span class="flex-1 truncate" x-text="subfolder.name"></span>
                                                <span class="text-[9px] opacity-50 tabular-nums" x-text="subfolder.count"></span>
                                            </button>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </nav>
                </aside>

                {{-- Right: media grid --}}
                <div class="flex-1 min-w-0 overflow-y-auto p-3 custom-scrollbar">

                    {{-- Empty state --}}
                    <template x-if="pickerFiltered.length === 0">
                        <div class="flex flex-col items-center justify-center h-48 text-center">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-white/[0.04] flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-gray-300 dark:text-white/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            </div>
                            <p class="text-[13px] font-semibold text-gray-600 dark:text-white/40 mb-1">No media found</p>
                            <p class="text-[11px] text-gray-400 dark:text-white/25">Try a different filter or upload new files</p>
                        </div>
                    </template>

                    {{-- Grid --}}
                    <template x-if="pickerFiltered.length > 0">
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2">
                            <template x-for="item in pickerFiltered" :key="item.id">
                                <div
                                    class="group relative bg-white dark:bg-[#1e2330] border border-gray-100 dark:border-white/[0.06] rounded-lg overflow-hidden cursor-pointer transition-all hover:border-gray-300 dark:hover:border-white/[0.2]"
                                    :class="pickerSelected.some(s => s.id === item.id) ? 'ring-2 ring-emerald-500 ring-offset-1 dark:ring-offset-[#161920]' : ''"
                                    @click="togglePickerSelect(item)"
                                >
                                    {{-- Thumbnail --}}
                                    <div class="aspect-square bg-gray-100 dark:bg-white/[0.04] overflow-hidden">
                                        <template x-if="item.mime_type && item.mime_type.startsWith('image/')">
                                            <img :src="item.thumbnail || item.url" :alt="item.name" loading="lazy" class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105">
                                        </template>
                                        <template x-if="!item.mime_type || !item.mime_type.startsWith('image/')">
                                            <div class="w-full h-full flex flex-col items-center justify-center gap-1 opacity-40">
                                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                <span class="text-[9px] uppercase tracking-wider font-semibold" x-text="item.mime_type?.split('/')[1] || 'FILE'"></span>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Checkmark overlay --}}
                                    <template x-if="pickerSelected.some(s => s.id === item.id)">
                                        <div class="absolute top-1.5 right-1.5 w-5 h-5 bg-emerald-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                        </div>
                                    </template>

                                    {{-- Filename --}}
                                    <div class="px-1.5 py-1 border-t border-gray-100 dark:border-white/[0.05]">
                                        <p class="text-[9px] text-gray-600 dark:text-white/40 truncate leading-tight" x-text="item.name"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                </div>{{-- end grid area --}}
            </div>{{-- end body --}}

            {{-- ── SELECTION PREVIEW + CONFIRM ──── --}}
            <div class="flex items-center gap-3 px-5 py-3.5 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50 dark:bg-white/[0.02] shrink-0">

                {{-- Selected thumbnails strip --}}
                <div class="flex-1 min-w-0 flex items-center gap-1.5">
                    <template x-if="pickerSelected.length === 0">
                        <p class="text-[11px] text-gray-400 dark:text-white/25 italic">No files selected</p>
                    </template>
                    <template x-if="pickerSelected.length > 0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <template x-for="sel in pickerSelected" :key="sel.id">
                                <div class="relative group/sel">
                                    <div class="w-8 h-8 bg-gray-200 dark:bg-white/[0.08] rounded-md overflow-hidden border-2 border-emerald-400">
                                        <template x-if="sel.mime_type && sel.mime_type.startsWith('image/')">
                                            <img :src="sel.thumbnail || sel.url" :alt="sel.name" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!sel.mime_type || !sel.mime_type.startsWith('image/')">
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-3.5 h-3.5 text-gray-400 dark:text-white/25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                            </div>
                                        </template>
                                    </div>
                                    <button
                                        @click.stop="removePickerSelect(sel.id)"
                                        class="absolute -top-1 -right-1 w-4 h-4 bg-gray-700 rounded-full flex items-center justify-center opacity-0 group-hover/sel:opacity-100 transition-opacity"
                                    >
                                        <svg class="w-2.5 h-2.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    </button>
                                </div>
                            </template>
                            <button @click="pickerSelected = []" class="text-[11px] text-gray-400 dark:text-white/25 hover:text-gray-600 dark:hover:text-white/50 transition-colors ml-1">Clear</button>
                        </div>
                    </template>
                </div>

                {{-- Action buttons --}}
                <div class="flex items-center gap-2 shrink-0">
                    <button
                        @click="close()"
                        class="px-4 py-1.5 text-[12px] font-medium text-gray-500 dark:text-white/40 hover:text-gray-700 dark:hover:text-white/70 transition-colors"
                    >Cancel</button>
                    {{--
                        @livewire: on confirm, emit 'mediaPickerSelected' with detail.
                        In Livewire component: #[On('mediaPickerSelected')] / wire:dispatch
                    --}}
                    <button
                        @click="confirmSelection()"
                        :disabled="pickerSelected.length === 0"
                        class="px-5 py-1.5 bg-emerald-500 hover:bg-emerald-600 disabled:opacity-40 disabled:cursor-not-allowed text-white text-[12px] font-semibold rounded-lg transition-all"
                    >
                        <span x-show="mode === 'single'">Use Selected File</span>
                        <span x-show="mode === 'multiple'">
                            Insert
                            <template x-if="pickerSelected.length > 0">
                                <span x-text="pickerSelected.length + ' file' + (pickerSelected.length > 1 ? 's' : '')"></span>
                            </template>
                        </span>
                    </button>
                </div>
            </div>

        </div>{{-- end modal panel --}}
    </div>{{-- end backdrop --}}
</div>{{-- end picker root --}}

@push('styles')
<style>
    /* Custom scrollbar for media picker */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #3f3f46;
        border-radius: 3px;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #52525b;
    }
    /* Firefox */
    .custom-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #d1d5db transparent;
    }
    .dark .custom-scrollbar {
        scrollbar-color: #3f3f46 transparent;
    }
</style>
@endpush

@push('scripts')
@verbatim
<script>
// ════════════════════════════════════════════════════════════════
//  ADMIN MEDIA PICKER — Alpine.js Component
//  @livewire: Replace allItems mock with $pickerMedia Livewire property.
//  Trigger: $dispatch('open-media-picker', { mode: 'single'|'multiple', target: 'fieldName' })
//  Result:  window.dispatchEvent(new CustomEvent('media-selected', { detail: { target, items } }))
// ════════════════════════════════════════════════════════════════
function adminMediaPicker() {
    return {
        isOpen:          false,
        mode:            'single',   // 'single' | 'multiple'
        target:          null,       // consumer-defined key, passed back in event
        pickerSelected:  [],         // [mediaItem, ...]
        pickerFiltered:  [],

        pfilters: { search: '', folder: 'all', type: 'all' },
        typeFilters: [
            { label: 'All',    value: 'all'   },
            { label: 'Images', value: 'image' },
            { label: 'Files',  value: 'file'  },
        ],

        // ── Mock data (same set as media library) ─────────────
        // @livewire: replace with $pickerMedia Livewire property
        // parent_id: null means folder is a root folder
        // parent_id: 'parent-folder-id' means this folder is nested inside that parent
        folders: [
            { id: 'all',                  name: 'All Media',              count: 20, parent_id: null,      _expanded: true  },
            { id: 'uncategorized',        name: 'Uncategorized',          count: 2,  parent_id: null,      _expanded: false },
            { id: 'products',             name: 'Product Images',         count: 8,  parent_id: null,      _expanded: true  },
            { id: 'products-color',       name: 'By Color',               count: 4,  parent_id: 'products', _expanded: false },
            { id: 'products-size',        name: 'By Size',                count: 3,  parent_id: 'products', _expanded: false },
            { id: 'products-detail',      name: 'Detail Shots',           count: 1,  parent_id: 'products', _expanded: false },
            { id: 'blog',                 name: 'Blog Images',            count: 3,  parent_id: null,      _expanded: false },
            { id: 'hero',                 name: 'Hero Banners',           count: 2,  parent_id: null,      _expanded: false },
            { id: 'collections',          name: 'Collections',            count: 2,  parent_id: null,      _expanded: false },
            { id: 'suppliers',            name: 'Supplier Assets',        count: 2,  parent_id: null,      _expanded: false },
            { id: 'brand',                name: 'Brand Assets',           count: 1,  parent_id: null,      _expanded: false },
        ],
        allItems: [
            { id: 1,  name: 'ankara-hero-banner.jpg',    url: 'https://picsum.photos/seed/ak1/800/600',  thumbnail: 'https://picsum.photos/seed/ak1/300/300',  folder_id: 'hero',          mime_type: 'image/jpeg',      size: 482300  },
            { id: 2,  name: 'premium-lace-001.jpg',      url: 'https://picsum.photos/seed/pl2/800/600',  thumbnail: 'https://picsum.photos/seed/pl2/300/300',  folder_id: 'products',      mime_type: 'image/jpeg',      size: 241000  },
            { id: 3,  name: 'aso-oke-detail.jpg',        url: 'https://picsum.photos/seed/ao3/800/600',  thumbnail: 'https://picsum.photos/seed/ao3/300/300',  folder_id: 'products',      mime_type: 'image/jpeg',      size: 319000  },
            { id: 4,  name: 'blog-fabric-care.jpg',      url: 'https://picsum.photos/seed/bf4/800/600',  thumbnail: 'https://picsum.photos/seed/bf4/300/300',  folder_id: 'blog',          mime_type: 'image/jpeg',      size: 185000  },
            { id: 5,  name: 'summer-collection.jpg',     url: 'https://picsum.photos/seed/sc5/800/600',  thumbnail: 'https://picsum.photos/seed/sc5/300/300',  folder_id: 'collections',   mime_type: 'image/jpeg',      size: 367000  },
            { id: 6,  name: 'ankara-print-red.jpg',      url: 'https://picsum.photos/seed/ap6/800/600',  thumbnail: 'https://picsum.photos/seed/ap6/300/300',  folder_id: 'products',      mime_type: 'image/jpeg',      size: 274000  },
            { id: 7,  name: 'headtie-gold-detail.jpg',   url: 'https://picsum.photos/seed/hg7/800/600',  thumbnail: 'https://picsum.photos/seed/hg7/300/300',  folder_id: 'products',      mime_type: 'image/jpeg',      size: 210000  },
            { id: 8,  name: 'size-guide.pdf',             url: '#',                                       thumbnail: null,                                     folder_id: 'uncategorized', mime_type: 'application/pdf', size: 94000   },
            { id: 9,  name: 'hero-women-banner.jpg',     url: 'https://picsum.photos/seed/hw9/800/600',  thumbnail: 'https://picsum.photos/seed/hw9/300/300',  folder_id: 'hero',          mime_type: 'image/jpeg',      size: 521000  },
            { id: 10, name: 'collection-ankara.webp',    url: 'https://picsum.photos/seed/ca10/800/600', thumbnail: 'https://picsum.photos/seed/ca10/300/300', folder_id: 'collections',   mime_type: 'image/webp',      size: 188000  },
            { id: 11, name: 'blog-trend-2025.jpg',       url: 'https://picsum.photos/seed/bt11/800/600', thumbnail: 'https://picsum.photos/seed/bt11/300/300', folder_id: 'blog',          mime_type: 'image/jpeg',      size: 243000  },
            { id: 12, name: 'cap-white-detail.jpg',      url: 'https://picsum.photos/seed/cw12/800/600', thumbnail: 'https://picsum.photos/seed/cw12/300/300', folder_id: 'products',      mime_type: 'image/jpeg',      size: 198000  },
            { id: 13, name: 'product-catalog-q2.pdf',    url: '#',                                       thumbnail: null,                                     folder_id: 'suppliers',     mime_type: 'application/pdf', size: 2100000 },
            { id: 14, name: 'lace-blue-closeup.jpg',     url: 'https://picsum.photos/seed/lb14/800/600', thumbnail: 'https://picsum.photos/seed/lb14/300/300', folder_id: 'products',      mime_type: 'image/jpeg',      size: 289000  },
            { id: 15, name: 'supplier-fabric-ref.png',   url: 'https://picsum.photos/seed/sf15/800/600', thumbnail: 'https://picsum.photos/seed/sf15/300/300', folder_id: 'suppliers',     mime_type: 'image/png',       size: 145000  },
            { id: 16, name: 'uncategorized-draft.jpg',   url: 'https://picsum.photos/seed/ud16/800/600', thumbnail: 'https://picsum.photos/seed/ud16/300/300', folder_id: 'uncategorized', mime_type: 'image/jpeg',      size: 163000  },
            { id: 17, name: 'ankara-green-swatch.jpg',   url: 'https://picsum.photos/seed/ag17/800/600', thumbnail: 'https://picsum.photos/seed/ag17/300/300', folder_id: 'products',      mime_type: 'image/jpeg',      size: 211000  },
            { id: 18, name: 'collection-womens.jpg',     url: 'https://picsum.photos/seed/cw18/800/600', thumbnail: 'https://picsum.photos/seed/cw18/300/300', folder_id: 'collections',   mime_type: 'image/jpeg',      size: 390000  },
            { id: 19, name: 'blog-styling-guide.jpg',    url: 'https://picsum.photos/seed/bs19/800/600', thumbnail: 'https://picsum.photos/seed/bs19/300/300', folder_id: 'blog',          mime_type: 'image/jpeg',      size: 277000  },
            { id: 20, name: 'brand-logo-dark.svg',       url: '#',                                       thumbnail: null,                                     folder_id: 'brand',         mime_type: 'image/svg+xml',   size: 8400    },
        ],

        init() { this.applyFilters(); },

        // ── Open / close ──────────────────────────────────────
        open(detail) {
            this.mode            = detail?.mode   || 'single';
            this.target          = detail?.target || null;
            this.pickerSelected  = [];
            this.pfilters        = { search: '', folder: 'all', type: 'all' };
            this.applyFilters();
            this.isOpen = true;
            document.body.style.overflow = 'hidden';
        },
        close() {
            this.isOpen = false;
            document.body.style.overflow = '';
        },

        // ── Filter ────────────────────────────────────────────
        applyFilters() {
            let items = [...this.allItems];
            if (this.pfilters.folder !== 'all')
                items = items.filter(i => i.folder_id === this.pfilters.folder);
            if (this.pfilters.type === 'image')
                items = items.filter(i => i.mime_type?.startsWith('image/'));
            else if (this.pfilters.type === 'file')
                items = items.filter(i => !i.mime_type?.startsWith('image/'));
            if (this.pfilters.search.trim()) {
                const q = this.pfilters.search.toLowerCase();
                items = items.filter(i => i.name.toLowerCase().includes(q));
            }
            this.pickerFiltered = items;
        },

        // ── Selection ─────────────────────────────────────────
        togglePickerSelect(item) {
            if (this.mode === 'single') {
                // Single mode: select and auto-confirm
                this.pickerSelected = [item];
                this.confirmSelection();
                return;
            }
            // Multiple mode: toggle
            const idx = this.pickerSelected.findIndex(s => s.id === item.id);
            if (idx === -1) this.pickerSelected.push(item);
            else this.pickerSelected.splice(idx, 1);
        },
        removePickerSelect(id) {
            this.pickerSelected = this.pickerSelected.filter(s => s.id !== id);
        },

        // ── Confirm ───────────────────────────────────────────
        // @livewire: wire:dispatch('mediaPickerConfirmed', { target, items })
        confirmSelection() {
            if (this.pickerSelected.length === 0) return;
            window.dispatchEvent(new CustomEvent('media-selected', {
                detail: { target: this.target, items: [...this.pickerSelected] }
            }));
            this.close();
        },
    };
}
</script>
@endverbatim
@endpush