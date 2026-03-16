{{--
    MediaPicker Livewire Component View
    File: resources/views/livewire/admin/media/media-picker.blade.php
    Component: App\Livewire\Admin\Media\MediaPicker

    Usage: <livewire:admin.media.media-picker />
    Trigger: @click="$dispatch('open-media-picker', { mode: 'single', target: 'mainImage' })"
    Listen:  @media-selected.window="handleMediaSelected($event.detail)"
--}}
<div
    x-data="adminMediaPicker()"
    x-init="init()"
    @open-media-picker.window="open($event.detail); $wire.loadMedia()"
    @media:picker-loaded.window="allItems = $event.detail.items; folders = $event.detail.folders; applyFilters()"
    @keydown.escape.window="isOpen && close()"
>
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

            {{-- HEADER --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-white/[0.06] shrink-0">
                <div>
                    <h2 class="text-[13px] font-semibold text-gray-900 dark:text-white">Select Media</h2>
                    <p class="text-[11px] text-gray-400 dark:text-white/30 mt-0.5">
                        <span x-show="mode === 'single'">Click any file to select it</span>
                        <span x-show="mode === 'multiple'">Click files to select, then confirm</span>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        @click="pickerModals.folder = true"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-medium rounded-lg border border-gray-200 dark:border-white/[0.08] text-gray-600 dark:text-white/50 hover:bg-gray-50 dark:hover:bg-white/[0.04] transition-all"
                    >
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
                        New Folder
                    </button>
                    <button
                        @click="pickerModals.upload = true"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-semibold rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white transition-all"
                    >
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
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

            {{-- TOOLBAR --}}
            <div class="flex items-center gap-3 px-5 py-2.5 border-b border-gray-100 dark:border-white/[0.06] bg-gray-50 dark:bg-white/[0.02] shrink-0 flex-wrap">
                <div class="relative flex-1 min-w-36 max-w-56">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 dark:text-white/25 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input
                        type="text"
                        x-model="pfilters.search"
                        @input.debounce.200ms="applyFilters()"
                        placeholder="Search files..."
                        class="w-full pl-9 pr-3 py-1.5 text-[12px] rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-800 dark:text-white/70 placeholder-gray-300 dark:placeholder-white/25 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    >
                </div>

                <select
                    x-model="pfilters.folder"
                    @change="applyFilters()"
                    class="px-3 py-1.5 text-[12px] rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-600 dark:text-white/50 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                >
                    <template x-for="f in folders" :key="f.id">
                        <option :value="f.id" x-text="f.name"></option>
                    </template>
                </select>

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

                <template x-if="pickerSelected.length > 0">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[11px] font-semibold rounded-full">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <span x-text="pickerSelected.length + ' selected'"></span>
                    </span>
                </template>
            </div>

            {{-- BODY --}}
            <div class="flex flex-1 min-h-0 overflow-hidden">

                {{-- Left: folder sidebar --}}
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

                    {{-- Loading state --}}
                    <template x-if="loading">
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2">
                            <template x-for="i in 12" :key="i">
                                <div class="aspect-square bg-gray-100 dark:bg-white/[0.05] rounded-lg animate-pulse"></div>
                            </template>
                        </div>
                    </template>

                    {{-- Empty state --}}
                    <template x-if="!loading && pickerFiltered.length === 0">
                        <div class="flex flex-col items-center justify-center h-48 text-center">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-white/[0.04] flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-gray-300 dark:text-white/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            </div>
                            <p class="text-[13px] font-semibold text-gray-600 dark:text-white/40 mb-1">No media found</p>
                            <p class="text-[11px] text-gray-400 dark:text-white/25">Try a different filter or upload files in the media library</p>
                        </div>
                    </template>

                    {{-- Grid --}}
                    <template x-if="!loading && pickerFiltered.length > 0">
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2">
                            <template x-for="item in pickerFiltered" :key="item.id">
                                <div
                                    class="group relative bg-white dark:bg-[#1e2330] border border-gray-100 dark:border-white/[0.06] rounded-lg overflow-hidden cursor-pointer transition-all hover:border-gray-300 dark:hover:border-white/[0.2]"
                                    :class="pickerSelected.some(s => s.id === item.id) ? 'ring-2 ring-emerald-500 ring-offset-1 dark:ring-offset-[#161920]' : ''"
                                    @click="togglePickerSelect(item)"
                                >
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

                                    <template x-if="pickerSelected.some(s => s.id === item.id)">
                                        <div class="absolute top-1.5 right-1.5 w-5 h-5 bg-emerald-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                        </div>
                                    </template>

                                    <div class="px-1.5 py-1 border-t border-gray-100 dark:border-white/[0.05]">
                                        <p class="text-[9px] text-gray-600 dark:text-white/40 truncate leading-tight" x-text="item.name"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                </div>
            </div>

            {{-- SELECTION PREVIEW + CONFIRM --}}
            <div class="flex items-center gap-3 px-5 py-3.5 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50 dark:bg-white/[0.02] shrink-0">
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

                <div class="flex items-center gap-2 shrink-0">
                    <button
                        @click="close()"
                        class="px-4 py-1.5 text-[12px] font-medium text-gray-500 dark:text-white/40 hover:text-gray-700 dark:hover:text-white/70 transition-colors"
                    >Cancel</button>
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

        </div>
    </div>

    {{-- ═══════════════════════════════════════
         MODAL: UPLOAD MEDIA (inside picker)
    ═══════════════════════════════════════ --}}
    <div
        x-show="pickerModals.upload"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
        @click.self="pickerModals.upload = false"
        style="display:none"
    >
        <div
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.08] rounded-xl w-full max-w-lg overflow-hidden"
        >
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-white/[0.06]">
                <div>
                    <h2 class="text-[13px] font-semibold text-gray-900 dark:text-white">Upload Media</h2>
                    <p class="text-[11px] text-gray-400 dark:text-white/30 mt-0.5">JPG, PNG, WebP, GIF, SVG, PDF · Max 10 MB each</p>
                </div>
                <button @click="pickerModals.upload = false" class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 dark:text-white/30 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-all">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="px-5 py-4 space-y-4">
                <div
                    class="border-2 border-dashed border-gray-200 dark:border-white/[0.1] rounded-lg p-8 text-center cursor-pointer transition-colors hover:border-emerald-400 dark:hover:border-emerald-500"
                    @click="$refs.pickerFileInput.click()"
                    @dragover.prevent="$el.classList.add('border-emerald-400')"
                    @dragleave.prevent="$el.classList.remove('border-emerald-400')"
                    @drop.prevent="handlePickerDrop($event); $el.classList.remove('border-emerald-400')"
                >
                    <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-white/[0.05] flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-gray-400 dark:text-white/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/>
                            <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                        </svg>
                    </div>
                    <p class="text-[13px] font-medium text-gray-700 dark:text-white/60">
                        Drop files here or <span class="text-emerald-500 dark:text-emerald-400">browse</span>
                    </p>
                    <p class="text-[11px] text-gray-400 dark:text-white/25 mt-1">Supports batch upload</p>
                </div>
                <input type="file" x-ref="pickerFileInput" multiple accept="image/*,.pdf,.svg" class="hidden" wire:model="pendingUploads" @change="handlePickerFileSelect($event)">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-medium text-gray-500 dark:text-white/40 mb-1.5">Upload to folder</label>
                        <select x-model="pickerUpload.folder" wire:model="uploadFolder" class="w-full px-3 py-1.5 text-[12px] rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-700 dark:text-white/70 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                            <template x-for="f in folders.filter(f => f.id !== 'all')" :key="f.id">
                                <option :value="f.id" x-text="f.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-gray-500 dark:text-white/40 mb-1.5">Default alt text</label>
                        <input type="text" x-model="pickerUpload.defaultAlt" wire:model="uploadDefaultAlt" placeholder="Optional" class="w-full px-3 py-1.5 text-[12px] rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-700 dark:text-white/70 placeholder-gray-300 dark:placeholder-white/20 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                    </div>
                </div>

                <template x-if="pickerUpload.queue.length > 0">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[11px] font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider" x-text="pickerUpload.queue.length + ' file(s) ready'"></p>
                            <button @click="pickerUpload.queue = []" class="text-[11px] text-gray-400 dark:text-white/25 hover:text-red-500 transition-colors">Clear all</button>
                        </div>
                        <div class="space-y-2 max-h-44 overflow-y-auto scrollbar-thin">
                            <template x-for="(file, idx) in pickerUpload.queue" :key="idx">
                                <div class="flex items-center gap-2.5 p-2 rounded-lg bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/[0.05]">
                                    <div class="w-8 h-8 bg-gray-200 dark:bg-white/[0.08] rounded-md overflow-hidden shrink-0 flex items-center justify-center">
                                        <template x-if="file.preview">
                                            <img :src="file.preview" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!file.preview">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-white/25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                        </template>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[12px] font-medium text-gray-800 dark:text-white/70 truncate" x-text="file.name"></p>
                                        <p class="text-[10px] text-gray-400 dark:text-white/25" x-text="formatPickerBytes(file.size)"></p>
                                        <div class="mt-1 h-0.5 bg-gray-200 dark:bg-white/[0.08] rounded-full overflow-hidden">
                                            <div class="h-full bg-emerald-500 rounded-full transition-all duration-300" :style="'width:' + (file.progress || 0) + '%'"></div>
                                        </div>
                                    </div>
                                    <div class="shrink-0">
                                        <template x-if="file.status === 'done'">
                                            <svg class="w-4 h-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                        </template>
                                        <template x-if="!file.status || file.status === 'pending'">
                                            <button @click="pickerUpload.queue.splice(idx, 1)" class="w-5 h-5 flex items-center justify-center rounded text-gray-300 dark:text-white/20 hover:text-red-500 transition-colors">
                                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex items-center justify-between px-5 py-3.5 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50 dark:bg-white/[0.02]">
                <p class="text-[11px] text-gray-400 dark:text-white/25" x-text="pickerUpload.queue.length > 0 ? pickerUpload.queue.length + ' file(s) queued' : 'No files selected'"></p>
                <div class="flex items-center gap-2">
                    <button @click="pickerModals.upload = false" class="px-3 py-1.5 text-[12px] font-medium text-gray-500 dark:text-white/40 hover:text-gray-700 dark:hover:text-white/70 transition-colors">Cancel</button>
                    <button
                        @click="startPickerUpload()"
                        :disabled="pickerUpload.queue.length === 0"
                        wire:loading.attr="disabled"
                        class="px-4 py-1.5 bg-emerald-500 hover:bg-emerald-600 disabled:opacity-40 disabled:cursor-not-allowed text-white text-[12px] font-semibold rounded-lg transition-all"
                    >
                        <span wire:loading.remove wire:target="doUpload">Upload <span x-show="pickerUpload.queue.length > 0" x-text="'(' + pickerUpload.queue.length + ')'"></span></span>
                        <span wire:loading wire:target="doUpload">Uploading…</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         MODAL: CREATE FOLDER (inside picker)
    ═══════════════════════════════════════ --}}
    <div
        x-show="pickerModals.folder"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
        @click.self="pickerModals.folder = false"
        style="display:none"
    >
        <div
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.08] rounded-xl w-full max-w-md overflow-hidden"
        >
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-white/[0.06]">
                <h2 class="text-[13px] font-semibold text-gray-900 dark:text-white">Create Folder</h2>
                <button @click="pickerModals.folder = false" class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 dark:text-white/30 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-all">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="px-5 py-4 space-y-3">
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/30 mb-1.5">Folder Name <span class="text-red-400">*</span></label>
                    <input
                        type="text"
                        x-model="pickerFolderForm.name"
                        @input="pickerFolderForm.slugEdited || (pickerFolderForm.slug = slugify(pickerFolderForm.name))"
                        placeholder="e.g. Product Hero Images"
                        @keydown.enter.prevent="savePickerFolder(true)"
                        class="w-full px-3 py-2 text-[13px] rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-white/20 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    >
                </div>
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/30 mb-1.5">Slug</label>
                    <input
                        type="text"
                        x-model="pickerFolderForm.slug"
                        @input="pickerFolderForm.slugEdited = true"
                        placeholder="auto-generated"
                        class="w-full px-3 py-2 text-[12px] font-mono rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-700 dark:text-white/70 placeholder-gray-300 dark:placeholder-white/20 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    >
                </div>
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/30 mb-1.5">
                        Parent Folder <span class="font-normal normal-case tracking-normal text-gray-300 dark:text-white/20">· optional</span>
                    </label>
                    <select
                        x-model="pickerFolderForm.parentId"
                        class="w-full px-3 py-2 text-[12px] rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-700 dark:text-white/70 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    >
                        <option value="">None (top-level)</option>
                        <template x-for="f in folders.filter(f => f.id !== 'all' && f.id !== 'uncategorized')" :key="f.id">
                            <option :value="f.id" x-text="f.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/30 mb-1.5">
                        Description <span class="font-normal normal-case tracking-normal text-gray-300 dark:text-white/20">· optional</span>
                    </label>
                    <textarea
                        x-model="pickerFolderForm.description"
                        rows="2"
                        placeholder="Brief description of this folder"
                        class="w-full px-3 py-2 text-[12px] rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-700 dark:text-white/70 placeholder-gray-300 dark:placeholder-white/20 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all resize-none"
                    ></textarea>
                </div>
            </div>
            <div class="flex items-center gap-2 px-5 py-3.5 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50 dark:bg-white/[0.02]">
                <button
                    @click="savePickerFolder(true)"
                    :disabled="pickerFolderSaving"
                    class="flex-1 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-[12px] font-semibold rounded-lg transition-all disabled:opacity-50"
                >
                    <span x-show="!pickerFolderSaving">Create Now</span>
                    <span x-show="pickerFolderSaving">Creating…</span>
                </button>
                <button @click="pickerModals.folder = false" class="px-4 py-1.5 text-[12px] font-medium text-gray-400 dark:text-white/25 hover:text-gray-600 dark:hover:text-white/50 transition-colors">Cancel</button>
            </div>
        </div>
    </div>

</div>

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 3px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #52525b; }
    .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #d1d5db transparent; }
    .dark .custom-scrollbar { scrollbar-color: #3f3f46 transparent; }
</style>
@endpush

@push('scripts')
<script>
function adminMediaPicker() {
    return {
        isOpen:         false,
        mode:           'single',
        target:         null,
        pickerSelected: [],
        pickerFiltered: [],
        loading:        false,

        pfilters: { search: '', folder: 'all', type: 'all' },
        typeFilters: [
            { label: 'All',    value: 'all'   },
            { label: 'Images', value: 'image' },
            { label: 'Files',  value: 'file'  },
        ],

        folders:  [],
        allItems: [],

        pickerModals: { upload: false, folder: false },
        pickerUpload: { queue: [], folder: 'uncategorized', defaultAlt: '' },
        pickerFolderForm: { name: '', slug: '', slugEdited: false, parentId: '', description: '' },
        pickerFolderSaving: false,

        init() { this.applyFilters(); },

        open(detail) {
            this.mode           = detail?.mode   || 'single';
            this.target         = detail?.target || null;
            this.pickerSelected = [];
            this.pfilters       = { search: '', folder: 'all', type: 'all' };
            this.loading        = true;
            this.isOpen         = true;
            document.body.style.overflow = 'hidden';
        },
        close() {
            this.isOpen = false;
            this.loading = false;
            this.pickerModals = { upload: false, folder: false };
            document.body.style.overflow = '';
        },

        applyFilters() {
            this.loading = false;
            let items = [...this.allItems];
            if (this.pfilters.folder !== 'all') {
                items = items.filter(i => i.folder_id === this.pfilters.folder);
            }
            if (this.pfilters.type === 'image') {
                items = items.filter(i => i.mime_type?.startsWith('image/'));
            } else if (this.pfilters.type === 'file') {
                items = items.filter(i => !i.mime_type?.startsWith('image/'));
            }
            if (this.pfilters.search.trim()) {
                const q = this.pfilters.search.toLowerCase();
                items = items.filter(i => i.name.toLowerCase().includes(q));
            }
            this.pickerFiltered = items;
        },

        togglePickerSelect(item) {
            if (this.mode === 'single') {
                this.pickerSelected = [item];
                this.confirmSelection();
                return;
            }
            const idx = this.pickerSelected.findIndex(s => s.id === item.id);
            if (idx === -1) { this.pickerSelected.push(item); } else { this.pickerSelected.splice(idx, 1); }
        },
        removePickerSelect(id) {
            this.pickerSelected = this.pickerSelected.filter(s => s.id !== id);
        },

        confirmSelection() {
            if (this.pickerSelected.length === 0) { return; }
            window.dispatchEvent(new CustomEvent('media-selected', {
                detail: { target: this.target, items: [...this.pickerSelected] }
            }));
            this.close();
        },

        // Upload methods
        handlePickerFileSelect(e) { this.addToPickerQueue(Array.from(e.target.files)); e.target.value = ''; },
        handlePickerDrop(e) { this.addToPickerQueue(Array.from(e.dataTransfer.files)); },
        addToPickerQueue(files) {
            files.forEach(file => {
                const entry = { name: file.name, size: file.size, preview: null, progress: 0, status: 'pending', file };
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = ev => { entry.preview = ev.target.result; };
                    reader.readAsDataURL(file);
                }
                this.pickerUpload.queue.push(entry);
            });
        },
        async startPickerUpload() {
            if (this.pickerUpload.queue.length === 0) { return; }
            this.pickerUpload.queue.forEach(f => { f.progress = 10; });
            const progressInterval = setInterval(() => {
                this.pickerUpload.queue.forEach(f => {
                    if (f.progress < 85) { f.progress = Math.min(f.progress + 15, 85); }
                });
            }, 300);
            await this.$wire.doUpload();
            clearInterval(progressInterval);
            this.pickerUpload.queue.forEach(f => { f.progress = 100; f.status = 'done'; });
            setTimeout(() => {
                this.pickerModals.upload = false;
                this.pickerUpload.queue = [];
            }, 800);
        },
        formatPickerBytes(bytes) {
            if (!bytes) { return '—'; }
            if (bytes < 1024) { return bytes + ' B'; }
            if (bytes < 1048576) { return (bytes / 1024).toFixed(1) + ' KB'; }
            return (bytes / 1048576).toFixed(1) + ' MB';
        },

        // Folder methods
        slugify(str) { return str.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, ''); },
        async savePickerFolder(closeAfter = true) {
            this.pickerFolderSaving = true;
            try {
                await this.$wire.saveFolder(
                    this.pickerFolderForm.name,
                    this.pickerFolderForm.slug,
                    this.pickerFolderForm.parentId,
                    this.pickerFolderForm.description
                );
                this.pickerFolderForm = { name: '', slug: '', slugEdited: false, parentId: '', description: '' };
                if (closeAfter) { this.pickerModals.folder = false; }
            } finally {
                this.pickerFolderSaving = false;
            }
        },
    };
}
</script>
@endpush
