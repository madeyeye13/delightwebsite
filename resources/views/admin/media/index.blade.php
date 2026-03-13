{{--
    Admin Media Library
    File: resources/views/admin/media/index.blade.php
    Stack: Blade + Alpine.js + Tailwind CSS
    Backend: Livewire-ready — every wire point is clearly commented
--}}

@extends('layouts.admin')

@section('title', 'Media Library')

@section('page-title', 'Media Library')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}"
       class="text-[11px] text-gray-400 dark:text-white/30 hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors">
        Admin
    </a>
    <svg class="w-3 h-3 text-gray-300 dark:text-white/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="9 18 15 12 9 6"/>
    </svg>
    <span class="text-[11px] text-gray-500 dark:text-white/40 font-medium">Media Library</span>
@endsection

@push('styles')
<style>
    /* Drag-over highlight */
    .dropzone-active {
        border-color: #10b981 !important;
        background-color: rgba(16, 185, 129, 0.04);
    }
    /* Media card selected ring */
    .media-card-selected {
        outline: 2px solid #10b981;
        outline-offset: -2px;
    }
    /* Loading skeleton shimmer */
    @keyframes shimmer-sweep {
        0%   { background-position: -400px 0; }
        100% { background-position:  400px 0; }
    }
    .skeleton-shimmer {
        background: linear-gradient(90deg, #e5e7eb 25%, #f3f4f6 50%, #e5e7eb 75%);
        background-size: 400px 100%;
        animation: shimmer-sweep 1.4s ease infinite;
    }
    .dark .skeleton-shimmer {
        background: linear-gradient(90deg, #1a1d24 25%, #22262f 50%, #1a1d24 75%);
        background-size: 400px 100%;
    }
    /* Folder active state */
    .folder-active {
        background-color: rgba(16, 185, 129, 0.08);
        color: #10b981;
    }
    .dark .folder-active {
        background-color: rgba(16, 185, 129, 0.1);
        color: #34d399;
    }
    /* Card staggered fade-in */
    @keyframes cardFadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .media-card-anim { animation: cardFadeIn 0.18s ease both; }
    /* Panel thin scrollbar */
    .scrollbar-thin::-webkit-scrollbar { width: 4px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 99px; }
    .dark .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); }
</style>
@endpush

@section('content')

{{--
    ════════════════════════════════════════════════════════════════
    ROOT ALPINE COMPONENT
    @livewire-swap: Replace x-data="adminMediaLibrary()" with
        wire:id binding when migrating to Livewire.
    ════════════════════════════════════════════════════════════════
--}}
<div
    x-data="adminMediaLibrary()"
    x-init="init()"
    class="-m-5 lg:-m-7 flex flex-col overflow-hidden"
    style="height: calc(100vh - 3.5rem);"
>

    {{-- ═══════════════════════════════════════
         MODAL: UPLOAD MEDIA
    ═══════════════════════════════════════ --}}
    <template x-teleport="body">
        <div
            x-show="modals.upload"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
            @click.self="modals.upload = false"
            @keydown.escape.window="modals.upload = false"
            style="display:none"
        >
            <div
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.08] rounded-xl w-full max-w-lg overflow-hidden"
            >
                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-white/[0.06]">
                    <div>
                        <h2 class="text-[13px] font-semibold text-gray-900 dark:text-white">Upload Media</h2>
                        <p class="text-[11px] text-gray-400 dark:text-white/30 mt-0.5">JPG, PNG, WebP, GIF, SVG, PDF · Max 10 MB each</p>
                    </div>
                    <button
                        @click="modals.upload = false"
                        class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 dark:text-white/30 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-all"
                    >
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <div class="px-5 py-4 space-y-4">
                    {{-- Dropzone --}}
                    <div
                        class="border-2 border-dashed border-gray-200 dark:border-white/[0.1] rounded-lg p-8 text-center cursor-pointer transition-colors hover:border-emerald-400 dark:hover:border-emerald-500"
                        @click="$refs.fileInput.click()"
                        @dragover.prevent="$el.classList.add('dropzone-active')"
                        @dragleave.prevent="$el.classList.remove('dropzone-active')"
                        @drop.prevent="handleDrop($event); $el.classList.remove('dropzone-active')"
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
                    <input type="file" x-ref="fileInput" multiple accept="image/*,.pdf,.svg" class="hidden" @change="handleFileSelect($event)">

                    {{-- Options --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500 dark:text-white/40 mb-1.5">Upload to folder</label>
                            {{-- @livewire: wire:model="uploadTargetFolder" --}}
                            <select x-model="upload.folder" class="w-full px-3 py-1.5 text-[12px] rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-700 dark:text-white/70 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                                <template x-for="f in folders.filter(f => f.id !== 'all')" :key="f.id">
                                    <option :value="f.id" x-text="f.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500 dark:text-white/40 mb-1.5">Default alt text</label>
                            <input type="text" x-model="upload.defaultAlt" placeholder="Optional" class="w-full px-3 py-1.5 text-[12px] rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-700 dark:text-white/70 placeholder-gray-300 dark:placeholder-white/20 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                        </div>
                    </div>

                    {{-- Upload queue --}}
                    <template x-if="upload.queue.length > 0">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[11px] font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wider" x-text="upload.queue.length + ' file(s) ready'"></p>
                                <button @click="upload.queue = []" class="text-[11px] text-gray-400 dark:text-white/25 hover:text-red-500 transition-colors">Clear all</button>
                            </div>
                            <div class="space-y-2 max-h-44 overflow-y-auto scrollbar-thin">
                                <template x-for="(file, idx) in upload.queue" :key="idx">
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
                                            <p class="text-[10px] text-gray-400 dark:text-white/25" x-text="formatBytes(file.size)"></p>
                                            <div class="mt-1 h-0.5 bg-gray-200 dark:bg-white/[0.08] rounded-full overflow-hidden">
                                                <div class="h-full bg-emerald-500 rounded-full transition-all duration-300" :style="'width:' + (file.progress || 0) + '%'"></div>
                                            </div>
                                        </div>
                                        <div class="shrink-0">
                                            <template x-if="file.status === 'done'">
                                                <svg class="w-4 h-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                            </template>
                                            <template x-if="file.status === 'error'">
                                                <svg class="w-4 h-4 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            </template>
                                            <template x-if="!file.status || file.status === 'pending'">
                                                <button @click="upload.queue.splice(idx, 1)" class="w-5 h-5 flex items-center justify-center rounded text-gray-300 dark:text-white/20 hover:text-red-500 transition-colors">
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

                {{-- Footer --}}
                <div class="flex items-center justify-between px-5 py-3.5 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50 dark:bg-white/[0.02]">
                    <p class="text-[11px] text-gray-400 dark:text-white/25" x-text="upload.queue.length > 0 ? upload.queue.length + ' file(s) queued' : 'No files selected'"></p>
                    <div class="flex items-center gap-2">
                        <button @click="modals.upload = false" class="px-3 py-1.5 text-[12px] font-medium text-gray-500 dark:text-white/40 hover:text-gray-700 dark:hover:text-white/70 transition-colors">Cancel</button>
                        {{-- @livewire: wire:click="doUpload" --}}
                        <button @click="mockUpload()" :disabled="upload.queue.length === 0" class="px-4 py-1.5 bg-emerald-500 hover:bg-emerald-600 disabled:opacity-40 disabled:cursor-not-allowed text-white text-[12px] font-semibold rounded-lg transition-all">
                            Upload <span x-show="upload.queue.length > 0" x-text="'(' + upload.queue.length + ')'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ═══════════════════════════════════════
         MODAL: CREATE FOLDER
    ═══════════════════════════════════════ --}}
    <template x-teleport="body">
        <div
            x-show="modals.folder"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
            @click.self="modals.folder = false"
            @keydown.escape.window="modals.folder = false"
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
                    <button @click="modals.folder = false" class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 dark:text-white/30 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-all">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/30 mb-1.5">Folder Name <span class="text-red-400">*</span></label>
                        <input type="text" x-model="folderForm.name" @input="folderForm.slugEdited || (folderForm.slug = slugify(folderForm.name))" placeholder="e.g. Product Hero Images" @keydown.enter.prevent="createFolder(false)" class="w-full px-3 py-2 text-[13px] rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-white/20 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/30 mb-1.5">Slug</label>
                        <input type="text" x-model="folderForm.slug" @input="folderForm.slugEdited = true" placeholder="auto-generated" class="w-full px-3 py-2 text-[12px] font-mono rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-700 dark:text-white/70 placeholder-gray-300 dark:placeholder-white/20 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/30 mb-1.5">
                            Parent Folder <span class="font-normal normal-case tracking-normal text-gray-300 dark:text-white/20">· optional</span>
                        </label>
                        <select x-model="folderForm.parentId" class="w-full px-3 py-2 text-[12px] rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-700 dark:text-white/70 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
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
                        <textarea x-model="folderForm.description" rows="2" placeholder="Brief description of this folder" class="w-full px-3 py-2 text-[12px] rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-700 dark:text-white/70 placeholder-gray-300 dark:placeholder-white/20 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all resize-none"></textarea>
                    </div>
                </div>
                <div class="flex items-center gap-2 px-5 py-3.5 border-t border-gray-100 dark:border-white/[0.06] bg-gray-50 dark:bg-white/[0.02]">
                    {{-- @livewire: wire:click="saveFolder" --}}
                    <button @click="createFolder(false)" class="flex-1 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-[12px] font-semibold rounded-lg transition-all">Create Now</button>
                    <button @click="createFolder(true)" class="flex-1 py-1.5 bg-gray-100 dark:bg-white/[0.06] text-gray-700 dark:text-white/60 text-[12px] font-semibold rounded-lg hover:bg-gray-200 dark:hover:bg-white/[0.1] transition-all">Create &amp; Add Another</button>
                    <button @click="modals.folder = false" class="px-4 py-1.5 text-[12px] font-medium text-gray-400 dark:text-white/25 hover:text-gray-600 dark:hover:text-white/50 transition-colors">Cancel</button>
                </div>
            </div>
        </div>
    </template>

    {{-- ═══════════════════════════════════════
         MEDIA DETAIL PANEL (right drawer)
         @livewire: bind detail.item to $selectedMedia Livewire property
    ═══════════════════════════════════════ --}}
    <template x-teleport="body">
        <div
            x-show="detail.item !== null"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 flex justify-end"
            @keydown.escape.window="detail.item = null"
            style="display:none"
        >
            <div @click="detail.item = null" class="absolute inset-0 bg-black/40"></div>
            <div
                x-show="detail.item !== null"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-180"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="relative z-10 w-full max-w-xs bg-white dark:bg-[#161920] border-l border-gray-100 dark:border-white/[0.08] flex flex-col h-full"
                style="display:none"
            >
                {{-- Panel header --}}
                <div class="flex items-center justify-between px-4 py-3.5 border-b border-gray-100 dark:border-white/[0.06] shrink-0">
                    <h3 class="text-[13px] font-semibold text-gray-900 dark:text-white">File Details</h3>
                    <button @click="detail.item = null" class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 dark:text-white/30 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition-all">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <template x-if="detail.item">
                    <div class="flex flex-col flex-1 overflow-y-auto scrollbar-thin min-h-0">
                        {{-- Preview --}}
                        <div class="aspect-video bg-gray-100 dark:bg-white/[0.04] flex items-center justify-center overflow-hidden border-b border-gray-100 dark:border-white/[0.06] shrink-0">
                            <template x-if="detail.item.mime_type && detail.item.mime_type.startsWith('image/')">
                                <img :src="detail.item.url" :alt="detail.item.name" class="w-full h-full object-contain">
                            </template>
                            <template x-if="!detail.item.mime_type || !detail.item.mime_type.startsWith('image/')">
                                <div class="flex flex-col items-center gap-2 opacity-40">
                                    <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    <span class="text-[11px] uppercase font-semibold tracking-wider" x-text="detail.item.mime_type"></span>
                                </div>
                            </template>
                        </div>

                        {{-- Editable fields --}}
                        <div class="px-4 py-4 space-y-3 flex-1">
                            <div>
                                <label class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/25 mb-1.5">Alt Text</label>
                                {{-- @livewire: wire:model.debounce="selectedMedia.alt" --}}
                                <input type="text" x-model="detail.item.alt" placeholder="Describe the image..." class="w-full px-2.5 py-1.5 text-[12px] rounded-md border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-800 dark:text-white/70 placeholder-gray-300 dark:placeholder-white/20 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/25 mb-1.5">Title</label>
                                <input type="text" x-model="detail.item.title" placeholder="Optional title" class="w-full px-2.5 py-1.5 text-[12px] rounded-md border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-800 dark:text-white/70 placeholder-gray-300 dark:placeholder-white/20 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/25 mb-1.5">Caption</label>
                                <textarea x-model="detail.item.caption" rows="2" placeholder="Optional caption" class="w-full px-2.5 py-1.5 text-[12px] rounded-md border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-800 dark:text-white/70 placeholder-gray-300 dark:placeholder-white/20 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all resize-none"></textarea>
                            </div>

                            {{-- Read-only meta --}}
                            <div class="pt-2 border-t border-gray-100 dark:border-white/[0.05]">
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/25 mb-2.5">File Info</p>
                                <dl class="grid grid-cols-2 gap-x-4 gap-y-2.5">
                                    <div>
                                        <dt class="text-[10px] text-gray-400 dark:text-white/25">Folder</dt>
                                        <dd class="text-[12px] text-gray-700 dark:text-white/60 font-medium mt-0.5 truncate" x-text="getFolderName(detail.item.folder_id)"></dd>
                                    </div>
                                    <div>
                                        <dt class="text-[10px] text-gray-400 dark:text-white/25">Type</dt>
                                        <dd class="text-[12px] text-gray-700 dark:text-white/60 font-medium mt-0.5 uppercase" x-text="detail.item.mime_type?.split('/')[1] || '—'"></dd>
                                    </div>
                                    <div>
                                        <dt class="text-[10px] text-gray-400 dark:text-white/25">Size</dt>
                                        <dd class="text-[12px] text-gray-700 dark:text-white/60 font-medium mt-0.5" x-text="formatBytes(detail.item.size)"></dd>
                                    </div>
                                    <div>
                                        <dt class="text-[10px] text-gray-400 dark:text-white/25">Dimensions</dt>
                                        <dd class="text-[12px] text-gray-700 dark:text-white/60 font-medium mt-0.5" x-text="detail.item.width && detail.item.height ? detail.item.width + ' × ' + detail.item.height : '—'"></dd>
                                    </div>
                                    <div class="col-span-2">
                                        <dt class="text-[10px] text-gray-400 dark:text-white/25">Uploaded</dt>
                                        <dd class="text-[12px] text-gray-700 dark:text-white/60 font-medium mt-0.5" x-text="detail.item.created_at"></dd>
                                    </div>
                                    <div class="col-span-2">
                                        <dt class="text-[10px] text-gray-400 dark:text-white/25 mb-1">File Name</dt>
                                        <dd class="text-[11px] font-mono text-gray-600 dark:text-white/50 break-all leading-relaxed" x-text="detail.item.name"></dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- URL copy --}}
                            <div>
                                <label class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/25 mb-1.5">URL</label>
                                <div class="flex gap-1.5">
                                    <input type="text" :value="detail.item.url" readonly class="flex-1 min-w-0 px-2.5 py-1.5 text-[11px] font-mono rounded-md border border-gray-200 dark:border-white/[0.08] bg-gray-50 dark:bg-white/[0.03] text-gray-500 dark:text-white/30 truncate">
                                    <button
                                        @click="copyUrl(detail.item.url)"
                                        :class="detail.copied ? 'bg-emerald-500 text-white' : 'bg-gray-100 dark:bg-white/[0.06] text-gray-500 dark:text-white/40 hover:bg-emerald-500/10 hover:text-emerald-500'"
                                        class="w-8 h-8 flex items-center justify-center rounded-md shrink-0 transition-all"
                                        :title="detail.copied ? 'Copied!' : 'Copy URL'"
                                    >
                                        <svg x-show="!detail.copied" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                        <svg x-show="detail.copied" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="px-4 py-3 border-t border-gray-100 dark:border-white/[0.06] space-y-2 shrink-0 bg-gray-50 dark:bg-white/[0.02]">
                            {{-- @livewire: wire:click="saveMediaMeta(detail.item.id)" --}}
                            <button class="w-full py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-[12px] font-semibold rounded-lg transition-all">Save Changes</button>
                            <div class="grid grid-cols-2 gap-2">
                                {{-- @livewire: wire:click="replaceMedia(detail.item.id)" --}}
                                <button class="py-1.5 bg-gray-100 dark:bg-white/[0.06] text-gray-600 dark:text-white/50 text-[12px] font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-white/[0.1] transition-all">Replace</button>
                                {{-- @livewire: wire:click="deleteMedia(detail.item.id)" --}}
                                <button class="py-1.5 bg-red-50 dark:bg-red-500/[0.07] text-red-500 text-[12px] font-medium rounded-lg hover:bg-red-100 dark:hover:bg-red-500/[0.14] transition-all">Delete</button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- ═══════════════════════════════════════
         PAGE HEADER
    ═══════════════════════════════════════ --}}
    <div class="flex items-center justify-between gap-4 px-5 lg:px-7 py-4 border-b border-gray-100 dark:border-white/[0.06] bg-white dark:bg-[#0d0f14] shrink-0">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                </svg>
            </div>
            <div class="min-w-0">
                <h1 class="text-[15px] font-semibold text-gray-900 dark:text-white leading-none">Media Library</h1>
                <p class="text-[11px] text-gray-400 dark:text-white/30 mt-0.5">Manage all uploaded images and files</p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            {{-- Bulk action bar --}}
            <template x-if="selectedIds.length > 0">
                <div class="flex items-center gap-2">
                    <span class="text-[12px] text-gray-500 dark:text-white/40" x-text="selectedIds.length + ' selected'"></span>
                    {{-- @livewire: wire:click="bulkMove" --}}
                    <button @click="bulkMove()" class="px-3 py-1.5 text-[12px] font-medium rounded-lg border border-gray-200 dark:border-white/[0.1] text-gray-600 dark:text-white/50 hover:border-emerald-400 hover:text-emerald-500 transition-all">Move</button>
                    {{-- @livewire: wire:click="bulkDelete" --}}
                    <button @click="bulkDelete()" class="px-3 py-1.5 text-[12px] font-medium rounded-lg border border-red-200 dark:border-red-500/25 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/[0.07] transition-all">Delete</button>
                    <button @click="selectedIds = []" class="text-[12px] text-gray-400 dark:text-white/25 hover:text-gray-600 dark:hover:text-white/50 transition-colors">Clear</button>
                    <div class="w-px h-4 bg-gray-200 dark:bg-white/[0.08]"></div>
                </div>
            </template>

            <button
                @click="modals.folder = true; resetFolderForm()"
                class="flex items-center gap-1.5 px-3 py-1.5 text-[12px] font-medium rounded-lg border border-gray-200 dark:border-white/[0.1] text-gray-600 dark:text-white/50 hover:border-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-400 transition-all"
            >
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                    <line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/>
                </svg>
                New Folder
            </button>

            <button
                @click="modals.upload = true"
                class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-[12px] font-semibold rounded-lg transition-all"
            >
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/>
                    <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                </svg>
                Upload Media
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         TOOLBAR
    ═══════════════════════════════════════ --}}
    <div class="flex items-center gap-3 px-5 lg:px-7 py-2.5 border-b border-gray-100 dark:border-white/[0.06] bg-white dark:bg-[#0d0f14] shrink-0 flex-wrap">

        {{-- Search --}}
        <div class="relative flex-1 min-w-40 max-w-64">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 dark:text-white/25 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            {{-- @livewire: wire:model.debounce.300ms="search" --}}
            <input
                type="text"
                x-model="filters.search"
                @input.debounce.250ms="applyFilters()"
                placeholder="Search files..."
                class="w-full pl-9 pr-3 py-1.5 text-[12px] rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-800 dark:text-white/70 placeholder-gray-300 dark:placeholder-white/25 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
            >
        </div>

        {{-- Type filter pills --}}
        <div class="flex items-center gap-1">
            <template x-for="t in typeFilters" :key="t.value">
                <button
                    @click="filters.type = t.value; applyFilters()"
                    :class="filters.type === t.value
                        ? 'bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 border-emerald-400/30'
                        : 'text-gray-500 dark:text-white/40 border-gray-200 dark:border-white/[0.08] hover:text-gray-700 dark:hover:text-white/60'"
                    class="px-2.5 py-1 text-[12px] font-medium rounded-lg border transition-all"
                    x-text="t.label"
                ></button>
            </template>
        </div>

        <div class="flex-1"></div>

        {{-- Sort --}}
        {{-- @livewire: wire:model="sort" --}}
        <select x-model="filters.sort" @change="applyFilters()" class="px-3 py-1.5 text-[12px] rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-600 dark:text-white/50 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
            <option value="newest">Newest first</option>
            <option value="oldest">Oldest first</option>
            <option value="name_asc">Name A–Z</option>
            <option value="name_desc">Name Z–A</option>
            <option value="size_asc">Smallest first</option>
            <option value="size_desc">Largest first</option>
        </select>

        {{-- View toggle --}}
        <div class="flex items-center border border-gray-200 dark:border-white/[0.08] rounded-lg overflow-hidden">
            <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-emerald-500/10 text-emerald-500 dark:text-emerald-400' : 'text-gray-400 dark:text-white/30 hover:text-gray-600 dark:hover:text-white/60'" class="w-8 h-8 flex items-center justify-center transition-all" title="Grid view">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
            </button>
            <div class="w-px h-4 bg-gray-200 dark:bg-white/[0.08]"></div>
            <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-emerald-500/10 text-emerald-500 dark:text-emerald-400' : 'text-gray-400 dark:text-white/30 hover:text-gray-600 dark:hover:text-white/60'" class="w-8 h-8 flex items-center justify-center transition-all" title="List view">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         MAIN BODY — folder sidebar + content
    ═══════════════════════════════════════ --}}
    <div class="flex flex-1 min-h-0 overflow-hidden">

        {{-- LEFT: FOLDER SIDEBAR --}}
        <aside class="hidden lg:flex flex-col w-48 shrink-0 border-r border-gray-100 dark:border-white/[0.06] bg-white dark:bg-[#0d0f14] overflow-y-auto scrollbar-thin">
            <div class="px-3 pt-3 pb-1">
                <p class="px-2 text-[10px] font-semibold tracking-widest uppercase text-gray-400 dark:text-white/25">Folders</p>
            </div>
            <nav class="flex-1 px-2 pb-3 space-y-0.5 mt-1">
                <template x-for="folder in folders" :key="folder.id">
                    <button
                        @click="selectFolder(folder.id)"
                        :class="[
                            'folder-item w-full flex items-center gap-2.5 px-2 py-1.5 rounded-lg text-left transition-all duration-150 text-[13px] font-medium',
                            filters.folder === folder.id
                                ? 'folder-active'
                                : 'text-gray-500 dark:text-white/40 hover:text-gray-800 dark:hover:text-white/70 hover:bg-gray-50 dark:hover:bg-white/[0.04]'
                        ]"
                    >
                        <template x-if="folder.id === 'all'">
                            <svg class="w-3.5 h-3.5 shrink-0 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                        </template>
                        <template x-if="folder.id !== 'all'">
                            <svg class="w-3.5 h-3.5 shrink-0 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                        </template>
                        <span class="flex-1 truncate" x-text="folder.name"></span>
                        <span class="text-[10px] tabular-nums shrink-0 opacity-60" x-text="folder.count"></span>
                    </button>
                </template>
            </nav>
            <div class="px-3 py-2 border-t border-gray-100 dark:border-white/[0.05]">
                <button @click="modals.folder = true; resetFolderForm()" class="w-full flex items-center gap-2 px-2 py-1.5 rounded-lg text-[12px] text-gray-400 dark:text-white/25 hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    New folder
                </button>
            </div>
        </aside>

        {{-- RIGHT: CONTENT AREA --}}
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden bg-gray-50 dark:bg-[#0a0c10]">

            {{-- Stats + select-all bar --}}
            <div class="flex items-center gap-3 px-5 py-2 border-b border-gray-100 dark:border-white/[0.04] bg-white dark:bg-[#0d0f14] shrink-0">
                <p class="text-[11px] text-gray-400 dark:text-white/30 flex-1">
                    Showing
                    <span class="font-semibold text-gray-700 dark:text-white/60" x-text="paginatedItems.length"></span>
                    of
                    <span class="font-semibold text-gray-700 dark:text-white/60" x-text="filteredItems.length"></span>
                    files
                    <template x-if="filters.folder !== 'all'">
                        <span> in <span class="text-emerald-500 dark:text-emerald-400 font-semibold" x-text="getFolderName(filters.folder)"></span></span>
                    </template>
                </p>
                <template x-if="paginatedItems.length > 0">
                    <label class="flex items-center gap-1.5 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            :checked="selectedIds.length > 0 && selectedIds.length === paginatedItems.length"
                            @change="toggleSelectAll($event.target.checked)"
                            class="w-3.5 h-3.5 rounded accent-emerald-500"
                        >
                        <span class="text-[11px] text-gray-400 dark:text-white/30">Select all</span>
                    </label>
                </template>
            </div>

            {{-- Scrollable media area --}}
            <div class="flex-1 overflow-y-auto scrollbar-none p-4 lg:p-5">

                {{-- Loading skeletons --}}
                <template x-if="ui.loading">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3">
                        <template x-for="i in 12" :key="i">
                            <div class="aspect-square skeleton-shimmer rounded-lg"></div>
                        </template>
                    </div>
                </template>

                {{-- Empty state: no media --}}
                <template x-if="!ui.loading && filteredItems.length === 0 && !filters.search">
                    <div class="flex flex-col items-center justify-center py-24 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-white/[0.04] flex items-center justify-center mb-5">
                            <svg class="w-7 h-7 text-gray-300 dark:text-white/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                        <h3 class="text-[14px] font-semibold text-gray-700 dark:text-white/50 mb-1">No media in this folder</h3>
                        <p class="text-[12px] text-gray-400 dark:text-white/25 mb-5">Upload your first file to get started</p>
                        <button @click="modals.upload = true" class="flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-[12px] font-semibold rounded-lg transition-all">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                            Upload Media
                        </button>
                    </div>
                </template>

                {{-- Empty state: search --}}
                <template x-if="!ui.loading && filteredItems.length === 0 && filters.search">
                    <div class="flex flex-col items-center justify-center py-24 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-white/[0.04] flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-gray-300 dark:text-white/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </div>
                        <h3 class="text-[14px] font-semibold text-gray-700 dark:text-white/50 mb-1">No results for "<span x-text="filters.search"></span>"</h3>
                        <p class="text-[12px] text-gray-400 dark:text-white/25 mb-4">Try a different search term</p>
                        <button @click="filters.search = ''; applyFilters()" class="text-[12px] text-emerald-500 dark:text-emerald-400 hover:underline">Clear search</button>
                    </div>
                </template>

                {{-- ══ GRID VIEW ══ --}}
                <template x-if="!ui.loading && filteredItems.length > 0 && viewMode === 'grid'">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3">
                        <template x-for="(item, idx) in paginatedItems" :key="item.id">
                            <div
                                class="media-card-anim group relative bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-lg overflow-hidden cursor-pointer transition-all hover:border-gray-300 dark:hover:border-white/[0.15]"
                                :class="selectedIds.includes(item.id) ? 'media-card-selected' : ''"
                                :style="'animation-delay:' + (idx * 0.02) + 's'"
                            >
                                {{-- Thumbnail --}}
                                <div class="aspect-square bg-gray-100 dark:bg-white/[0.04] overflow-hidden" @click="openDetail(item)">
                                    <template x-if="item.mime_type && item.mime_type.startsWith('image/')">
                                        <img :src="item.thumbnail || item.url" :alt="item.name" loading="lazy" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                    </template>
                                    <template x-if="!item.mime_type || !item.mime_type.startsWith('image/')">
                                        <div class="w-full h-full flex flex-col items-center justify-center gap-1.5 opacity-40">
                                            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                            <span class="text-[10px] font-semibold uppercase tracking-wider" x-text="item.mime_type?.split('/')[1] || 'FILE'"></span>
                                        </div>
                                    </template>
                                </div>

                                {{-- Checkbox --}}
                                <div class="absolute top-2 left-2">
                                    <input type="checkbox" :checked="selectedIds.includes(item.id)" @change="toggleSelect(item.id)" @click.stop class="w-3.5 h-3.5 rounded accent-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity" :class="selectedIds.includes(item.id) ? '!opacity-100' : ''">
                                </div>

                                {{-- Kebab menu --}}
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity" x-data="{ open: false }">
                                    <button @click.stop="open = !open" class="w-6 h-6 rounded-md bg-black/50 flex items-center justify-center hover:bg-black/70 transition-all text-white">
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" class="absolute right-0 top-7 w-36 bg-white dark:bg-[#1e2330] border border-gray-100 dark:border-white/[0.08] rounded-lg overflow-hidden z-20" style="display:none">
                                        <button @click.stop="openDetail(item); open = false" class="w-full text-left px-3 py-1.5 text-[12px] text-gray-600 dark:text-white/50 hover:bg-gray-50 dark:hover:bg-white/[0.04] hover:text-gray-900 dark:hover:text-white/80 transition-colors">View details</button>
                                        <button @click.stop="copyUrl(item.url); open = false" class="w-full text-left px-3 py-1.5 text-[12px] text-gray-600 dark:text-white/50 hover:bg-gray-50 dark:hover:bg-white/[0.04] hover:text-gray-900 dark:hover:text-white/80 transition-colors">Copy URL</button>
                                        <button @click.stop="toggleSelect(item.id); open = false" class="w-full text-left px-3 py-1.5 text-[12px] text-gray-600 dark:text-white/50 hover:bg-gray-50 dark:hover:bg-white/[0.04] hover:text-gray-900 dark:hover:text-white/80 transition-colors">Select</button>
                                        <div class="border-t border-gray-100 dark:border-white/[0.05]"></div>
                                        {{-- @livewire: wire:click="deleteMedia(item.id)" --}}
                                        <button @click.stop="open = false" class="w-full text-left px-3 py-1.5 text-[12px] text-red-500 hover:bg-red-50 dark:hover:bg-red-500/[0.06] transition-colors">Delete</button>
                                    </div>
                                </div>

                                {{-- Card footer --}}
                                <div class="px-2.5 py-2 border-t border-gray-100 dark:border-white/[0.05]">
                                    <p class="text-[11px] font-medium text-gray-800 dark:text-white/60 truncate leading-tight" x-text="item.name"></p>
                                    <p class="text-[10px] text-gray-400 dark:text-white/25 mt-0.5" x-text="formatBytes(item.size)"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- ══ LIST VIEW ══ --}}
                <template x-if="!ui.loading && filteredItems.length > 0 && viewMode === 'list'">
                    <div class="bg-white dark:bg-[#161920] border border-gray-100 dark:border-white/[0.06] rounded-xl overflow-hidden">
                        {{-- List header --}}
                        <div class="grid items-center px-4 py-2.5 border-b border-gray-100 dark:border-white/[0.05] bg-gray-50 dark:bg-white/[0.02]"
                             style="grid-template-columns: 2rem 2.5rem 1fr 8rem 5rem 6rem 7rem 4.5rem">
                            <div></div><div></div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/25">Name</p>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/25">Folder</p>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/25">Type</p>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/25">Size</p>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/25">Dimensions</p>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-white/25">Uploaded</p>
                        </div>
                        {{-- List rows --}}
                        <template x-for="(item, idx) in paginatedItems" :key="item.id">
                            <div
                                class="grid items-center px-4 py-2.5 border-b border-gray-50 dark:border-white/[0.03] last:border-b-0 hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors cursor-pointer"
                                style="grid-template-columns: 2rem 2.5rem 1fr 8rem 5rem 6rem 7rem 4.5rem"
                                :class="selectedIds.includes(item.id) ? 'bg-emerald-500/[0.03]' : ''"
                                @click="openDetail(item)"
                            >
                                <input type="checkbox" :checked="selectedIds.includes(item.id)" @change="toggleSelect(item.id)" @click.stop class="w-3.5 h-3.5 rounded accent-emerald-500">
                                <div class="w-8 h-8 bg-gray-100 dark:bg-white/[0.05] rounded-md overflow-hidden flex items-center justify-center shrink-0">
                                    <template x-if="item.mime_type && item.mime_type.startsWith('image/')">
                                        <img :src="item.thumbnail || item.url" :alt="item.name" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!item.mime_type || !item.mime_type.startsWith('image/')">
                                        <svg class="w-3.5 h-3.5 text-gray-400 dark:text-white/25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </template>
                                </div>
                                <p class="text-[12px] font-medium text-gray-800 dark:text-white/60 truncate pr-2" x-text="item.name"></p>
                                <p class="text-[11px] text-gray-500 dark:text-white/35 truncate pr-2" x-text="getFolderName(item.folder_id)"></p>
                                <p class="text-[11px] text-gray-400 dark:text-white/25 uppercase" x-text="item.mime_type?.split('/')[1] || '—'"></p>
                                <p class="text-[11px] text-gray-400 dark:text-white/25" x-text="formatBytes(item.size)"></p>
                                <p class="text-[11px] text-gray-400 dark:text-white/25" x-text="item.width && item.height ? item.width + '×' + item.height : '—'"></p>
                                <p class="text-[11px] text-gray-400 dark:text-white/25" x-text="item.created_at"></p>
                            </div>
                        </template>
                    </div>
                </template>

            </div>{{-- end scroll --}}

            {{-- ══ PAGINATION ══ --}}
            <template x-if="!ui.loading && filteredItems.length > perPage">
                <div class="flex items-center justify-between gap-4 px-5 py-3 border-t border-gray-100 dark:border-white/[0.06] bg-white dark:bg-[#0d0f14] shrink-0">
                    <p class="text-[11px] text-gray-400 dark:text-white/30 shrink-0">
                        Showing
                        <span class="font-semibold text-gray-700 dark:text-white/60" x-text="((currentPage - 1) * perPage) + 1"></span>–<span class="font-semibold text-gray-700 dark:text-white/60" x-text="Math.min(currentPage * perPage, filteredItems.length)"></span>
                        of <span class="font-semibold text-gray-700 dark:text-white/60" x-text="filteredItems.length"></span>
                    </p>
                    <div class="flex items-center gap-1">
                        <button @click="currentPage > 1 && currentPage--" :disabled="currentPage === 1" class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 dark:border-white/[0.08] text-gray-400 dark:text-white/30 hover:border-emerald-400 hover:text-emerald-500 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                        </button>
                        <template x-for="page in totalPages" :key="page">
                            <button @click="currentPage = page" :class="currentPage === page ? 'bg-emerald-500 text-white border-emerald-500' : 'border-gray-200 dark:border-white/[0.08] text-gray-500 dark:text-white/40 hover:border-emerald-400 hover:text-emerald-500'" class="w-7 h-7 flex items-center justify-center rounded-lg border text-[11px] font-semibold transition-all" x-text="page"></button>
                        </template>
                        <button @click="currentPage < totalPages && currentPage++" :disabled="currentPage === totalPages" class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 dark:border-white/[0.08] text-gray-400 dark:text-white/30 hover:border-emerald-400 hover:text-emerald-500 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                    <select x-model.number="perPage" @change="currentPage = 1" class="px-2 py-1 text-[11px] rounded-lg border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#0d0f14] text-gray-500 dark:text-white/40 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                        <option value="24">24 / page</option>
                        <option value="48">48 / page</option>
                        <option value="96">96 / page</option>
                    </select>
                </div>
            </template>

        </div>{{-- end content --}}
    </div>{{-- end main body --}}
</div>{{-- end root --}}

@endsection

@push('scripts')
@verbatim
<script>
// ════════════════════════════════════════════════════════════════
//  ADMIN MEDIA LIBRARY — Alpine.js Component
//  @livewire: Replace x-data="adminMediaLibrary()" with
//      wire:id + Alpine.data entangle() when moving to Livewire.
//      Each backend integration point is annotated below.
// ════════════════════════════════════════════════════════════════
function adminMediaLibrary() {
    return {

        // ── UI ────────────────────────────────────────────────
        viewMode:    'grid',
        selectedIds: [],
        currentPage: 1,
        perPage:     24,
        ui: { loading: false },

        // ── Filters ───────────────────────────────────────────
        // @livewire: wire:model each property
        filters: {
            search: '',
            folder: 'all',
            type:   'all',
            sort:   'newest',
        },
        typeFilters: [
            { label: 'All',    value: 'all'   },
            { label: 'Images', value: 'image' },
            { label: 'Files',  value: 'file'  },
        ],

        // ── Modals ────────────────────────────────────────────
        modals: { upload: false, folder: false },

        // ── Upload ────────────────────────────────────────────
        // @livewire: wire:model="uploadTargetFolder" · wire:click="doUpload"
        upload: {
            queue:      [],
            folder:     'uncategorized',
            defaultAlt: '',
        },

        // ── Folder form ───────────────────────────────────────
        // @livewire: wire:click="saveFolder"
        folderForm: {
            name: '', slug: '', slugEdited: false, parentId: '', description: '',
        },

        // ── Detail panel ──────────────────────────────────────
        // @livewire: wire:model="selectedMedia"
        detail: { item: null, copied: false },

        // ── Mock folders ──────────────────────────────────────
        // @livewire: replace with $folders Livewire property
        folders: [
            { id: 'all',           name: 'All Media',       count: 20 },
            { id: 'uncategorized', name: 'Uncategorized',   count: 2  },
            { id: 'products',      name: 'Product Images',  count: 8  },
            { id: 'blog',          name: 'Blog Images',     count: 3  },
            { id: 'hero',          name: 'Hero Banners',    count: 2  },
            { id: 'collections',   name: 'Collections',     count: 2  },
            { id: 'suppliers',     name: 'Supplier Assets', count: 2  },
            { id: 'brand',         name: 'Brand Assets',    count: 1  },
        ],

        // ── Mock media items ──────────────────────────────────
        // @livewire: replace with $mediaItems Livewire property
        // Shape: { id, name, url, thumbnail, folder_id, mime_type,
        //          size, width, height, created_at, alt, title, caption }
        allItems: [
            { id: 1,  name: 'ankara-hero-banner.jpg',    url: 'https://picsum.photos/seed/ak1/800/600',  thumbnail: 'https://picsum.photos/seed/ak1/300/300',  folder_id: 'hero',          mime_type: 'image/jpeg',      size: 482300,  width: 1920, height: 1080, created_at: '2025-06-01', alt: '', title: '', caption: '' },
            { id: 2,  name: 'premium-lace-001.jpg',      url: 'https://picsum.photos/seed/pl2/800/600',  thumbnail: 'https://picsum.photos/seed/pl2/300/300',  folder_id: 'products',      mime_type: 'image/jpeg',      size: 241000,  width: 1200, height: 1200, created_at: '2025-06-02', alt: '', title: '', caption: '' },
            { id: 3,  name: 'aso-oke-detail.jpg',        url: 'https://picsum.photos/seed/ao3/800/600',  thumbnail: 'https://picsum.photos/seed/ao3/300/300',  folder_id: 'products',      mime_type: 'image/jpeg',      size: 319000,  width: 1200, height: 800,  created_at: '2025-06-03', alt: '', title: '', caption: '' },
            { id: 4,  name: 'blog-fabric-care.jpg',      url: 'https://picsum.photos/seed/bf4/800/600',  thumbnail: 'https://picsum.photos/seed/bf4/300/300',  folder_id: 'blog',          mime_type: 'image/jpeg',      size: 185000,  width: 900,  height: 600,  created_at: '2025-06-04', alt: '', title: '', caption: '' },
            { id: 5,  name: 'summer-collection.jpg',     url: 'https://picsum.photos/seed/sc5/800/600',  thumbnail: 'https://picsum.photos/seed/sc5/300/300',  folder_id: 'collections',   mime_type: 'image/jpeg',      size: 367000,  width: 1600, height: 900,  created_at: '2025-06-05', alt: '', title: '', caption: '' },
            { id: 6,  name: 'ankara-print-red.jpg',      url: 'https://picsum.photos/seed/ap6/800/600',  thumbnail: 'https://picsum.photos/seed/ap6/300/300',  folder_id: 'products',      mime_type: 'image/jpeg',      size: 274000,  width: 1200, height: 1200, created_at: '2025-06-05', alt: '', title: '', caption: '' },
            { id: 7,  name: 'headtie-gold-detail.jpg',   url: 'https://picsum.photos/seed/hg7/800/600',  thumbnail: 'https://picsum.photos/seed/hg7/300/300',  folder_id: 'products',      mime_type: 'image/jpeg',      size: 210000,  width: 1200, height: 1200, created_at: '2025-06-06', alt: '', title: '', caption: '' },
            { id: 8,  name: 'size-guide.pdf',             url: '#',                                       thumbnail: null,                                     folder_id: 'uncategorized', mime_type: 'application/pdf', size: 94000,   width: null, height: null, created_at: '2025-06-06', alt: '', title: '', caption: '' },
            { id: 9,  name: 'hero-women-banner.jpg',     url: 'https://picsum.photos/seed/hw9/800/600',  thumbnail: 'https://picsum.photos/seed/hw9/300/300',  folder_id: 'hero',          mime_type: 'image/jpeg',      size: 521000,  width: 1920, height: 1080, created_at: '2025-06-07', alt: '', title: '', caption: '' },
            { id: 10, name: 'collection-ankara.webp',    url: 'https://picsum.photos/seed/ca10/800/600', thumbnail: 'https://picsum.photos/seed/ca10/300/300', folder_id: 'collections',   mime_type: 'image/webp',      size: 188000,  width: 1400, height: 900,  created_at: '2025-06-08', alt: '', title: '', caption: '' },
            { id: 11, name: 'blog-trend-2025.jpg',       url: 'https://picsum.photos/seed/bt11/800/600', thumbnail: 'https://picsum.photos/seed/bt11/300/300', folder_id: 'blog',          mime_type: 'image/jpeg',      size: 243000,  width: 900,  height: 600,  created_at: '2025-06-08', alt: '', title: '', caption: '' },
            { id: 12, name: 'cap-white-detail.jpg',      url: 'https://picsum.photos/seed/cw12/800/600', thumbnail: 'https://picsum.photos/seed/cw12/300/300', folder_id: 'products',      mime_type: 'image/jpeg',      size: 198000,  width: 1200, height: 1200, created_at: '2025-06-09', alt: '', title: '', caption: '' },
            { id: 13, name: 'product-catalog-q2.pdf',    url: '#',                                       thumbnail: null,                                     folder_id: 'suppliers',     mime_type: 'application/pdf', size: 2100000, width: null, height: null, created_at: '2025-06-09', alt: '', title: '', caption: '' },
            { id: 14, name: 'lace-blue-closeup.jpg',     url: 'https://picsum.photos/seed/lb14/800/600', thumbnail: 'https://picsum.photos/seed/lb14/300/300', folder_id: 'products',      mime_type: 'image/jpeg',      size: 289000,  width: 1200, height: 1200, created_at: '2025-06-10', alt: '', title: '', caption: '' },
            { id: 15, name: 'supplier-fabric-ref.png',   url: 'https://picsum.photos/seed/sf15/800/600', thumbnail: 'https://picsum.photos/seed/sf15/300/300', folder_id: 'suppliers',     mime_type: 'image/png',       size: 145000,  width: 800,  height: 800,  created_at: '2025-06-11', alt: '', title: '', caption: '' },
            { id: 16, name: 'uncategorized-draft.jpg',   url: 'https://picsum.photos/seed/ud16/800/600', thumbnail: 'https://picsum.photos/seed/ud16/300/300', folder_id: 'uncategorized', mime_type: 'image/jpeg',      size: 163000,  width: 900,  height: 600,  created_at: '2025-06-11', alt: '', title: '', caption: '' },
            { id: 17, name: 'ankara-green-swatch.jpg',   url: 'https://picsum.photos/seed/ag17/800/600', thumbnail: 'https://picsum.photos/seed/ag17/300/300', folder_id: 'products',      mime_type: 'image/jpeg',      size: 211000,  width: 1200, height: 1200, created_at: '2025-06-12', alt: '', title: '', caption: '' },
            { id: 18, name: 'collection-womens.jpg',     url: 'https://picsum.photos/seed/cw18/800/600', thumbnail: 'https://picsum.photos/seed/cw18/300/300', folder_id: 'collections',   mime_type: 'image/jpeg',      size: 390000,  width: 1600, height: 900,  created_at: '2025-06-12', alt: '', title: '', caption: '' },
            { id: 19, name: 'blog-styling-guide.jpg',    url: 'https://picsum.photos/seed/bs19/800/600', thumbnail: 'https://picsum.photos/seed/bs19/300/300', folder_id: 'blog',          mime_type: 'image/jpeg',      size: 277000,  width: 900,  height: 600,  created_at: '2025-06-13', alt: '', title: '', caption: '' },
            { id: 20, name: 'brand-logo-dark.svg',       url: '#',                                       thumbnail: null,                                     folder_id: 'brand',         mime_type: 'image/svg+xml',   size: 8400,    width: 400,  height: 200,  created_at: '2025-06-14', alt: '', title: '', caption: '' },
        ],
        filteredItems: [],

        // ── Init ──────────────────────────────────────────────
        init() { this.applyFilters(); },

        // ── Computed ──────────────────────────────────────────
        get paginatedItems() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredItems.slice(start, start + this.perPage);
        },
        get totalPages() {
            return Math.max(1, Math.ceil(this.filteredItems.length / this.perPage));
        },

        // ── Filter & sort ─────────────────────────────────────
        // @livewire: replace with Livewire computed property
        applyFilters() {
            let items = [...this.allItems];
            if (this.filters.folder !== 'all')
                items = items.filter(i => i.folder_id === this.filters.folder);
            if (this.filters.type === 'image')
                items = items.filter(i => i.mime_type?.startsWith('image/'));
            else if (this.filters.type === 'file')
                items = items.filter(i => !i.mime_type?.startsWith('image/'));
            if (this.filters.search.trim()) {
                const q = this.filters.search.toLowerCase();
                items = items.filter(i => i.name.toLowerCase().includes(q));
            }
            const dir = (a, b, k) => a[k] < b[k] ? -1 : a[k] > b[k] ? 1 : 0;
            switch (this.filters.sort) {
                case 'newest':    items.sort((a, b) => dir(b, a, 'created_at')); break;
                case 'oldest':    items.sort((a, b) => dir(a, b, 'created_at')); break;
                case 'name_asc':  items.sort((a, b) => dir(a, b, 'name'));       break;
                case 'name_desc': items.sort((a, b) => dir(b, a, 'name'));       break;
                case 'size_asc':  items.sort((a, b) => a.size - b.size);         break;
                case 'size_desc': items.sort((a, b) => b.size - a.size);         break;
            }
            this.filteredItems = items;
            this.currentPage = 1;
        },

        // ── Folder helpers ────────────────────────────────────
        selectFolder(id) { this.filters.folder = id; this.applyFilters(); },
        getFolderName(id) { return this.folders.find(f => f.id === id)?.name || 'Uncategorized'; },

        // ── Selection ─────────────────────────────────────────
        toggleSelect(id) {
            const idx = this.selectedIds.indexOf(id);
            if (idx === -1) this.selectedIds.push(id); else this.selectedIds.splice(idx, 1);
        },
        toggleSelectAll(checked) {
            this.selectedIds = checked ? this.paginatedItems.map(i => i.id) : [];
        },

        // ── Bulk actions ──────────────────────────────────────
        // @livewire: wire:click="bulkMove" / wire:click="bulkDelete"
        bulkMove()   { alert('Move ' + this.selectedIds.length + ' item(s) — backend pending'); },
        bulkDelete() { alert('Delete ' + this.selectedIds.length + ' item(s) — backend pending'); },

        // ── Detail panel ──────────────────────────────────────
        openDetail(item) { this.detail.item = { ...item }; this.detail.copied = false; },
        copyUrl(url) {
            navigator.clipboard.writeText(url).catch(() => {});
            this.detail.copied = true;
            setTimeout(() => { this.detail.copied = false; }, 2000);
        },

        // ── Folder modal ──────────────────────────────────────
        // @livewire: POST /admin/media/folders
        slugify(str) { return str.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, ''); },
        resetFolderForm() {
            this.folderForm = { name: '', slug: '', slugEdited: false, parentId: '', description: '' };
        },
        createFolder(andAnother) {
            if (!this.folderForm.name.trim()) return;
            const id = this.folderForm.slug || this.slugify(this.folderForm.name);
            if (this.folders.some(f => f.id === id)) { alert('Folder slug already exists.'); return; }
            this.folders.push({ id, name: this.folderForm.name.trim(), count: 0 });
            // @livewire: $emit('folderCreated', { id, name })
            if (andAnother) this.resetFolderForm();
            else this.modals.folder = false;
        },

        // ── File upload ───────────────────────────────────────
        // @livewire: replace with wire:click="doUpload" + FormData POST
        handleFileSelect(e) { this.addToQueue(Array.from(e.target.files)); e.target.value = ''; },
        handleDrop(e) { this.addToQueue(Array.from(e.dataTransfer.files)); },
        addToQueue(files) {
            files.forEach(file => {
                const entry = { name: file.name, size: file.size, preview: null, progress: 0, status: 'pending', file };
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = ev => { entry.preview = ev.target.result; };
                    reader.readAsDataURL(file);
                }
                this.upload.queue.push(entry);
            });
        },
        // Simulated upload — replace entirely with Livewire upload
        mockUpload() {
            this.upload.queue.forEach((file, i) => {
                if (file.status !== 'pending') return;
                let p = 0;
                const t = setInterval(() => {
                    p += Math.random() * 28;
                    file.progress = Math.min(Math.round(p), 100);
                    if (file.progress >= 100) {
                        clearInterval(t);
                        file.status = 'done';
                        // @livewire: $emit('mediaUploaded') → refresh allItems
                    }
                }, 180 + i * 60);
            });
        },

        // ── Utility ───────────────────────────────────────────
        formatBytes(bytes) {
            if (!bytes) return '—';
            if (bytes < 1024)     return bytes + ' B';
            if (bytes < 1048576)  return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        },
    };
}
</script>
@endverbatim
@endpush