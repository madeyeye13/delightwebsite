{{--
╔══════════════════════════════════════════════════════════════════╗
║  ADMIN BLOG POST CREATE / EDIT FORM                               ║
║  Sections: Basic, Content (Quill), SEO                            ║
║  Sidebar: Featured Image, Publish, Category, Tags                 ║
╚══════════════════════════════════════════════════════════════════╝
--}}

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">
<style>
    .ql-toolbar.ql-snow {
        border-color: #d1d5db;
        border-radius: 8px 8px 0 0;
        background: #f9fafb;
        padding: 6px 8px;
    }
    .dark .ql-toolbar.ql-snow { border-color: #374151; background: #111827; }
    .ql-container.ql-snow {
        border-color: #d1d5db;
        border-radius: 0 0 8px 8px;
        font-size: 0.875rem;
        min-height: 300px;
    }
    .dark .ql-container.ql-snow { border-color: #374151; background: #111827; color: #f9fafb; }
    .dark .ql-editor.ql-blank::before { color: #6b7280; }
    .dark .ql-snow .ql-stroke { stroke: #9ca3af; }
    .dark .ql-snow .ql-fill { fill: #9ca3af; }
    .dark .ql-snow .ql-picker { color: #9ca3af; }
    .dark .ql-snow .ql-picker-options { background-color: #1f2937; border-color: #374151; }
    .ql-toolbar .ql-cta {
        font-size: 10px !important;
        font-weight: 700 !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        letter-spacing: 0.04em !important;
        color: #1F6F67 !important;
        width: auto !important;
        padding: 0 7px !important;
        border: 1px solid #1F6F67 !important;
        border-radius: 3px !important;
    }
    .dark .ql-toolbar .ql-cta { color: #33A89F !important; border-color: #33A89F !important; }
    .tag-pill { animation: tagIn 0.15s ease; }
    @keyframes tagIn {
        from { opacity: 0; transform: scale(0.85); }
        to   { opacity: 1; transform: scale(1); }
    }
    .modal-backdrop { backdrop-filter: blur(2px); }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #E4E4E7; border-radius: 3px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #3F3F46; }
</style>
@endpush

<div x-data="blogFormManager()" class="space-y-6" @media-selected.window="handleMediaSelected($event.detail)">

<script>
    window.__blogFormData = {
        categories: {!! $categoriesJson !!},
        tags: {!! $tagsJson !!},
        post: {!! $postJson !!},
    };
</script>

    {{-- ════════════════════════════════════════════════
         ADD CATEGORY MODAL
    ════════════════════════════════════════════════ --}}
    <template x-teleport="body">
    <div
        x-show="modals.category"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="modal-backdrop fixed inset-0 z-[9999] flex items-start justify-center pt-20 sm:items-center sm:pt-0 bg-black/50 p-4"
        @click.self="modals.category = false"
        style="display:none"
    >
        <div
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-neutral-50 dark:bg-[#1a2332] rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-2xl w-full max-w-md"
        >
            <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                <h2 class="font-semibold text-neutral-900 dark:text-neutral-50">Add Blog Category</h2>
                <button @click="modals.category = false" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors text-neutral-500 dark:text-neutral-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Category Name *</label>
                    <input
                        type="text"
                        x-model="categoryModal.name"
                        @input="categoryModal.slugEdited || (categoryModal.slug = slugify(categoryModal.name))"
                        placeholder="e.g. Fabric Care Tips"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm"
                        @keydown.enter="createCategory(false)"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Slug *</label>
                    <input
                        type="text"
                        x-model="categoryModal.slug"
                        @input="categoryModal.slugEdited = true"
                        placeholder="auto-generated"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm font-mono"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Description <span class="text-neutral-400 font-normal">(optional)</span></label>
                    <textarea
                        x-model="categoryModal.description"
                        rows="2"
                        placeholder="Brief description"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm"
                    ></textarea>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="categoryModal.isActive" class="w-4 h-4">
                    <span class="text-sm text-neutral-700 dark:text-neutral-300">Active</span>
                </label>
            </div>
            <div class="flex items-center gap-2 px-5 py-4 border-t border-neutral-200 dark:border-neutral-700">
                <button @click="createCategory(false)" class="flex-1 px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand-600 transition-colors font-medium text-sm">Create Now</button>
                <button @click="createCategory(true)" class="flex-1 px-4 py-2 bg-neutral-200 dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 rounded-lg hover:bg-neutral-300 dark:hover:bg-neutral-700 transition-colors font-medium text-sm">Create &amp; Add Another</button>
                <button @click="modals.category = false" class="px-4 py-2 text-sm text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-50 transition-colors">Cancel</button>
            </div>
        </div>
    </div>
    </template>

    {{-- ════════════════════════════════════════════════
         INSERT CTA BUTTON MODAL
    ════════════════════════════════════════════════ --}}
    <template x-teleport="body">
    <div
        x-show="modals.cta"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="modal-backdrop fixed inset-0 z-[9999] flex items-start justify-center pt-20 sm:items-center sm:pt-0 bg-black/50 p-4"
        @click.self="modals.cta = false"
        style="display:none"
    >
        <div
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-neutral-50 dark:bg-[#1a2332] rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-2xl w-full max-w-sm"
        >
            <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                <div>
                    <h2 class="font-semibold text-neutral-900 dark:text-neutral-50">Insert CTA Button</h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Inserts a styled call-to-action button into your post</p>
                </div>
                <button @click="modals.cta = false" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors text-neutral-500 dark:text-neutral-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Button Label *</label>
                    <input
                        type="text"
                        x-model="ctaModal.text"
                        placeholder="e.g. Shop Ankara Fabrics"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm"
                        @keydown.enter.prevent="insertCta()"
                        x-ref="ctaTextInput"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">URL *</label>
                    <input
                        type="url"
                        x-model="ctaModal.url"
                        placeholder="https://..."
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm"
                        @keydown.enter.prevent="insertCta()"
                    >
                </div>
                {{-- Preview --}}
                <div x-show="ctaModal.text || ctaModal.url">
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-1.5">Preview</p>
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-neutral-50 text-xs font-bold tracking-wider uppercase font-sans">
                        <span x-text="ctaModal.text || 'Button label'"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2 px-5 py-4 border-t border-neutral-200 dark:border-neutral-700">
                <button
                    @click="insertCta()"
                    :disabled="!ctaModal.text.trim() || !ctaModal.url.trim()"
                    class="flex-1 px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand-600 transition-colors font-medium text-sm disabled:opacity-40 disabled:cursor-not-allowed"
                >Insert Button</button>
                <button @click="modals.cta = false" class="px-4 py-2 text-sm text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-50 transition-colors">Cancel</button>
            </div>
        </div>
    </div>
    </template>

    {{-- ════════════════════════════════════════════════
         HEADER
    ════════════════════════════════════════════════ --}}
    <div class="flex items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.blog.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <nav class="flex items-center gap-2 text-xs text-neutral-600 dark:text-neutral-400 mb-1">
                    <a href="{{ route('admin.blog.index') }}" class="hover:text-neutral-900 dark:hover:text-neutral-50">Blog Posts</a>
                    <svg class="w-3 h-3 text-neutral-400 dark:text-neutral-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="9 18 15 12 9 6"/></svg>
                    <span class="text-neutral-900 dark:text-neutral-50 font-medium">{{ isset($post) && $post ? 'Edit Post' : 'Create Post' }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-50">{{ isset($post) && $post ? 'Edit Post' : 'Create Post' }}</h1>
                <p class="text-xs text-neutral-600 dark:text-neutral-400 mt-0.5">Craft and publish blog posts with SEO metadata</p>
            </div>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <button @click="expandAllSections()" class="text-brand dark:text-brand-300 hover:text-brand-600 dark:hover:text-brand-200 font-medium">Expand All</button>
            <span class="text-neutral-400 dark:text-neutral-600">—</span>
            <button @click="collapseAllSections()" class="text-brand dark:text-brand-300 hover:text-brand-600 dark:hover:text-brand-200 font-medium">Collapse All</button>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════
         TWO-COLUMN LAYOUT
    ════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ─── MAIN FORM AREA (2/3) ─────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- ══ BASIC INFORMATION ══ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800">
                <button @click="sections.basic = !sections.basic" class="w-full px-5 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand dark:text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-50">Basic Information</h3>
                    </div>
                    <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400 transition-transform" :class="sections.basic && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </button>
                <div x-show="sections.basic" class="border-t border-neutral-200 dark:border-neutral-800 px-5 py-4 space-y-4">

                    {{-- Title + Slug --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Post Title *</label>
                            <input
                                type="text"
                                x-model="form.title"
                                @input="autoGenerateSlug()"
                                placeholder="e.g. How to Care for Ankara Fabric"
                                class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Slug *</label>
                            <input
                                type="text"
                                x-model="form.slug"
                                @input="slugManuallyEdited = true"
                                placeholder="auto-generated"
                                class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm font-mono"
                            >
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1" x-show="!slugManuallyEdited">Auto-generated from title</p>
                            <p class="text-xs text-amber-500 dark:text-amber-400 mt-1" x-show="slugManuallyEdited">Custom slug (manually set)</p>
                        </div>
                    </div>

                    {{-- Excerpt --}}
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Excerpt</label>
                        <textarea
                            x-model="form.excerpt"
                            rows="3"
                            placeholder="Short summary shown on blog listing pages and social sharing..."
                            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm resize-none"
                        ></textarea>
                        <div class="flex justify-between mt-1">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Displayed in blog listings and meta description fallback</p>
                            <span class="text-xs" :class="form.excerpt.length > 160 ? 'text-amber-500' : 'text-neutral-400'" x-text="form.excerpt.length + '/160'"></span>
                        </div>
                    </div>

                    {{-- Author --}}
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Author Name</label>
                        <input
                            type="text"
                            x-model="form.author"
                            placeholder="e.g. Amaka Okonkwo"
                            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm"
                        >
                    </div>

                </div>
            </div>

            {{-- ══ CONTENT ══ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800">
                <button @click="sections.content = !sections.content" class="w-full px-5 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand dark:text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-50">Post Content</h3>
                    </div>
                    <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400 transition-transform" :class="sections.content && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </button>
                <div x-show="sections.content" class="border-t border-neutral-200 dark:border-neutral-800 px-5 py-4">
                    <div wire:ignore id="quill-editor" class="rounded-lg overflow-hidden border border-neutral-300 dark:border-neutral-700">
                        <div id="quill-toolbar">
                            <span class="ql-formats">
                                <select class="ql-header"><option selected></option><option value="2">H2</option><option value="3">H3</option><option value="4">H4</option></select>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-bold"></button>
                                <button class="ql-italic"></button>
                                <button class="ql-underline"></button>
                                <button class="ql-strike"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-blockquote"></button>
                                <button class="ql-code-block"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-list" value="ordered"></button>
                                <button class="ql-list" value="bullet"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-link"></button>
                                <button class="ql-image"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-cta" title="Insert CTA Button">CTA</button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-clean"></button>
                            </span>
                        </div>
                        <div id="quill-body" style="min-height:300px"></div>
                    </div>
                    <textarea x-model="form.bodyHtml" id="body-hidden" class="hidden"></textarea>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2">Use the editor above to write rich content with headings, lists, images, and links.</p>
                </div>
            </div>

            {{-- ══ SEO ══ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800">
                <button @click="sections.seo = !sections.seo" class="w-full px-5 py-3 flex items-center justify-between hover:bg-neutral-100 dark:hover:bg-neutral-900 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand dark:text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <div class="flex items-center gap-2">
                            <h3 class="font-semibold text-neutral-900 dark:text-neutral-50">SEO &amp; Meta</h3>
                            <span class="text-xs text-neutral-400 dark:text-neutral-500 font-normal">(optional — auto-filled from title/excerpt if blank)</span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-400 transition-transform" :class="sections.seo && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </button>
                <div x-show="sections.seo" class="border-t border-neutral-200 dark:border-neutral-800 px-5 py-4 space-y-4">

                    {{-- Meta Title --}}
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Meta Title</label>
                        <input
                            type="text"
                            x-model="form.metaTitle"
                            :placeholder="form.title || 'Auto-filled from post title'"
                            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm"
                        >
                        <div class="flex justify-between mt-1">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Ideal: 50–60 characters</p>
                            <span class="text-xs" :class="form.metaTitle.length > 60 ? 'text-amber-500' : 'text-neutral-400'" x-text="form.metaTitle.length + '/60'"></span>
                        </div>
                    </div>

                    {{-- Meta Description --}}
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Meta Description</label>
                        <textarea
                            x-model="form.metaDescription"
                            rows="2"
                            :placeholder="form.excerpt || 'Auto-filled from excerpt'"
                            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm resize-none"
                        ></textarea>
                        <div class="flex justify-between mt-1">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Ideal: 150–160 characters</p>
                            <span class="text-xs" :class="form.metaDescription.length > 160 ? 'text-amber-500' : 'text-neutral-400'" x-text="form.metaDescription.length + '/160'"></span>
                        </div>
                    </div>

                    {{-- OG Image URL --}}
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Open Graph Image URL <span class="text-neutral-400 font-normal">(optional — falls back to featured image)</span></label>
                        <div class="flex gap-2">
                            <input
                                type="url"
                                x-model="form.ogImage"
                                placeholder="https://..."
                                class="flex-1 px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm"
                            >
                            <button
                                @click="openMediaLibrary('og')"
                                class="px-3 py-2 bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors text-xs font-medium flex items-center gap-1 whitespace-nowrap"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                Pick
                            </button>
                        </div>
                    </div>

                    {{-- SERP Preview --}}
                    <div class="rounded-lg border border-neutral-200 dark:border-neutral-700 p-4 bg-white dark:bg-neutral-900">
                        <p class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-3">Google Preview</p>
                        <div class="space-y-1">
                            <p class="text-[#1a0dab] dark:text-[#8ab4f8] text-sm font-medium leading-tight" x-text="(form.metaTitle || form.title) || 'Post title will appear here'"></p>
                            <p class="text-xs text-[#006621] dark:text-[#34a853]">yoursite.com/blog/<span x-text="form.slug || 'post-slug'"></span></p>
                            <p class="text-xs text-neutral-600 dark:text-neutral-400 leading-relaxed" x-text="(form.metaDescription || form.excerpt) || 'Meta description or excerpt will appear here.'"></p>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- ─── SIDEBAR (1/3) ─────────────────────────────────── --}}
        <div class="space-y-4">

            {{-- ══ FEATURED IMAGE ══ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-5">
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-50 mb-3 text-sm">Featured Image</h3>

                {{-- Image preview --}}
                <div
                    class="relative rounded-lg overflow-hidden bg-neutral-100 dark:bg-neutral-900 border-2 border-dashed border-neutral-300 dark:border-neutral-700 mb-3 cursor-pointer hover:border-brand dark:hover:border-brand-400 transition-colors"
                    style="aspect-ratio: 16/9"
                    @click="openMediaLibrary('featured')"
                >
                    <img
                        x-show="form.featuredImageUrl"
                        :src="form.featuredImageUrl"
                        alt="Featured image preview"
                        class="w-full h-full object-cover"
                    >
                    <div x-show="!form.featuredImageUrl" class="absolute inset-0 flex flex-col items-center justify-center gap-2">
                        <svg class="w-8 h-8 text-neutral-400 dark:text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Click to pick from media library</p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button
                        @click="openMediaLibrary('featured')"
                        class="flex-1 px-3 py-2 bg-brand text-white rounded-lg hover:bg-brand-600 transition-colors text-xs font-medium flex items-center justify-center gap-1"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span x-text="form.featuredImageUrl ? 'Change Image' : 'Pick from Library'"></span>
                    </button>
                    <button
                        x-show="form.featuredImageUrl"
                        @click="form.featuredImageUrl = ''"
                        class="px-3 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors text-xs font-medium"
                    >
                        Remove
                    </button>
                </div>

                {{-- Alt text --}}
                <div class="mt-3" x-show="form.featuredImageUrl">
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Alt Text</label>
                    <input
                        type="text"
                        x-model="form.featuredImageAlt"
                        placeholder="Describe the image for accessibility"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-xs"
                    >
                </div>
            </div>

            {{-- ══ PUBLISH SETTINGS ══ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-5">
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-50 mb-4 text-sm">Publish Settings</h3>

                {{-- Status indicator --}}
                <div class="flex items-center gap-2 mb-4 p-3 rounded-lg bg-neutral-100 dark:bg-neutral-800">
                    <span
                        class="w-2 h-2 rounded-full flex-shrink-0"
                        :class="{
                            'bg-green-500': form.status === 'published',
                            'bg-amber-500': form.status === 'scheduled',
                            'bg-neutral-400': form.status === 'draft'
                        }"
                    ></span>
                    <span class="text-xs font-medium text-neutral-700 dark:text-neutral-300 capitalize" x-text="form.status"></span>
                    <span class="ml-auto text-xs text-neutral-500 dark:text-neutral-400" x-show="form.publishedAt" x-text="'Published ' + form.publishedAt"></span>
                </div>

                {{-- Scheduled date (only for scheduled status) --}}
                <div class="mb-4" x-show="form.status === 'scheduled'">
                    <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">Schedule Date &amp; Time *</label>
                    <input
                        type="datetime-local"
                        x-model="form.scheduledAt"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-xs"
                    >
                </div>

                {{-- Action buttons --}}
                <div class="space-y-2">
                    <button
                        @click="save('draft')"
                        :disabled="saving"
                        class="w-full px-4 py-2.5 bg-neutral-200 dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 rounded-lg hover:bg-neutral-300 dark:hover:bg-neutral-700 transition-colors font-medium text-sm flex items-center justify-center gap-2 disabled:opacity-50"
                    >
                        <svg x-show="saving && saveAction === 'draft'" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <svg x-show="!saving || saveAction !== 'draft'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        Save as Draft
                    </button>
                    <button
                        @click="save('scheduled')"
                        :disabled="saving || !form.scheduledAt"
                        class="w-full px-4 py-2.5 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors font-medium text-sm flex items-center justify-center gap-2 disabled:opacity-50"
                    >
                        <svg x-show="saving && saveAction === 'scheduled'" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <svg x-show="!saving || saveAction !== 'scheduled'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Schedule Post
                    </button>
                    <button
                        @click="save('published')"
                        :disabled="saving"
                        class="w-full px-4 py-2.5 bg-brand text-white rounded-lg hover:bg-brand-600 transition-colors font-bold text-sm flex items-center justify-center gap-2 disabled:opacity-50"
                    >
                        <svg x-show="saving && saveAction === 'published'" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <svg x-show="!saving || saveAction !== 'published'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Publish Now
                    </button>
                </div>
            </div>

            {{-- ══ CATEGORY ══ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-neutral-900 dark:text-neutral-50 text-sm">Category</h3>
                    <button
                        @click="openCategoryModal()"
                        class="text-xs font-medium text-brand dark:text-brand-300 hover:text-brand-600 dark:hover:text-brand-200 flex items-center gap-1 transition-colors"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add New
                    </button>
                </div>
                <select
                    x-model="form.categoryId"
                    class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-lg text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900 focus:ring-2 focus:ring-brand focus:border-transparent text-sm"
                >
                    <option value="">— No Category —</option>
                    <template x-for="cat in categories" :key="cat.id">
                        <option :value="cat.id" x-text="cat.name"></option>
                    </template>
                </select>
            </div>

            {{-- ══ TAGS ══ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-5">
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-50 mb-3 text-sm">Tags</h3>
                <div class="border border-neutral-300 dark:border-neutral-700 rounded-lg p-2 bg-white dark:bg-neutral-900 focus-within:ring-2 focus-within:ring-brand focus-within:border-transparent">
                    <div class="flex flex-wrap gap-1.5 mb-1.5" x-show="form.tags.length > 0">
                        <template x-for="(tag, tIdx) in form.tags" :key="tag">
                            <span class="tag-pill inline-flex items-center gap-1 px-2.5 py-1 bg-brand/10 dark:bg-brand/20 text-brand dark:text-brand-300 rounded-full text-xs font-medium">
                                <span x-text="tag"></span>
                                <button @click="removeTag(tIdx)" class="hover:text-brand-700 dark:hover:text-brand-100 transition-colors leading-none">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </span>
                        </template>
                    </div>
                    <input
                        type="text"
                        x-model="tagInput"
                        @keydown.enter.prevent="addTag()"
                        @keydown="if($event.key === ',') { $event.preventDefault(); addTag(); }"
                        :list="'tag-suggestions'"
                        placeholder="Type tag and press Enter or comma..."
                        class="w-full border-none outline-none text-sm text-neutral-900 dark:text-neutral-50 bg-transparent placeholder-neutral-400 dark:placeholder-neutral-500"
                    >
                    <datalist id="tag-suggestions">
                        <template x-for="suggestion in tagSuggestions" :key="suggestion">
                            <option :value="suggestion"></option>
                        </template>
                    </datalist>
                </div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Press Enter or comma to add. Click × to remove.</p>
            </div>

            {{-- ══ POST OPTIONS ══ --}}
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-5">
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-50 mb-3 text-sm">Post Options</h3>
                <div class="space-y-3">
                    <label class="flex items-center justify-between gap-3 cursor-pointer">
                        <div>
                            <p class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Featured Post</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Highlighted in the featured section</p>
                        </div>
                        <button
                            @click="form.featured = !form.featured"
                            :class="form.featured ? 'bg-brand' : 'bg-neutral-300 dark:bg-neutral-700'"
                            class="relative inline-flex h-5 w-9 rounded-full transition-colors duration-150 focus:outline-none flex-shrink-0"
                        >
                            <span
                                :class="form.featured ? 'translate-x-4' : 'translate-x-0.5'"
                                class="inline-block w-4 h-4 mt-0.5 rounded-full bg-white shadow transition-transform duration-150"
                            ></span>
                        </button>
                    </label>
                </div>
            </div>

            {{-- ══ COMMENTS (edit mode only) ══ --}}
            @if(isset($post) && $post && $post->comments()->exists())
            <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-5">
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-50 mb-3 text-sm">
                    Comments
                    <span class="ml-1 text-xs text-neutral-500 dark:text-neutral-400 font-normal">({{ $post->comments()->count() }} total)</span>
                </h3>
                <div class="space-y-2 max-h-64 overflow-y-auto custom-scrollbar">
                    @foreach($post->comments()->with('replies')->whereNull('parent_id')->latest()->get() as $comment)
                    <div class="bg-white dark:bg-neutral-900 rounded-lg p-3 border border-neutral-200 dark:border-neutral-700">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-neutral-900 dark:text-neutral-50">{{ $comment->name }}</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $comment->email }} · {{ $comment->created_at->diffForHumans() }}</p>
                                <p class="text-xs text-neutral-700 dark:text-neutral-300 mt-1 line-clamp-2">{{ $comment->body }}</p>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                @if(!$comment->is_approved)
                                <button
                                    wire:click="approveComment({{ $comment->id }})"
                                    class="px-2 py-1 text-xs bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded hover:bg-green-100 transition-colors"
                                >Approve</button>
                                @else
                                <span class="px-2 py-1 text-xs bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded">Approved</span>
                                @endif
                                <button
                                    wire:click="deleteComment({{ $comment->id }})"
                                    class="px-2 py-1 text-xs bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded hover:bg-red-100 transition-colors"
                                >Delete</button>
                            </div>
                        </div>
                        @if($comment->replies->isNotEmpty())
                        <div class="mt-2 pl-3 border-l-2 border-neutral-200 dark:border-neutral-700 space-y-1">
                            @foreach($comment->replies as $reply)
                            <p class="text-xs text-neutral-600 dark:text-neutral-400"><span class="font-medium text-neutral-700 dark:text-neutral-300">{{ $reply->name }}:</span> {{ Str::limit($reply->body, 80) }}</p>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>
<script>
function blogFormManager() {
    return {
        slugManuallyEdited: false,
        saving: false,
        saveAction: '',
        quillEditor: null,
        tagInput: '',
        mediaTarget: 'featured',

        modals: {
            category: false,
            cta: false,
        },

        ctaModal: { text: '', url: '' },

        categoryModal: {
            name: '',
            slug: '',
            slugEdited: false,
            description: '',
            isActive: true,
        },

        categories: window.__blogFormData?.categories || [],
        tagSuggestions: window.__blogFormData?.tags || [],

        sections: {
            basic: true,
            content: true,
            seo: false,
        },

        form: {
            id: null,
            title: '',
            slug: '',
            excerpt: '',
            author: '',
            body: '',
            bodyHtml: '',
            featuredImageUrl: '',
            featuredImageAlt: '',
            categoryId: '',
            tags: [],
            metaTitle: '',
            metaDescription: '',
            ogImage: '',
            featured: false,
            status: 'draft',
            scheduledAt: '',
            publishedAt: '',
        },

        init() {
            const saved = window.__blogFormData?.post;
            if (saved) {
                Object.assign(this.form, saved);
            }
            this.$nextTick(() => { this.initQuill(); });
        },

        initQuill() {
            if (this.quillEditor) {
                this.quillEditor.off('text-change');
                this.quillEditor = null;
            }
            const body = document.getElementById('quill-body');
            if (!body) { return; }
            if (body.classList.contains('ql-editor')) { return; }

            this.quillEditor = new Quill('#quill-body', {
                modules: { toolbar: '#quill-toolbar' },
                theme: 'snow',
                placeholder: 'Start writing your blog post here...',
            });

            // Hook custom CTA toolbar button
            const ctaBtn = document.querySelector('#quill-toolbar .ql-cta');
            if (ctaBtn) {
                ctaBtn.addEventListener('click', () => {
                    this.ctaModal = { text: '', url: '' };
                    this.modals.cta = true;
                    this.$nextTick(() => this.$refs.ctaTextInput?.focus());
                });
            }

            if (this.form.bodyHtml) {
                this.quillEditor.clipboard.dangerouslyPasteHTML(this.form.bodyHtml);
            }

            this.quillEditor.on('text-change', () => {
                this.form.body = this.quillEditor.getText();
                this.form.bodyHtml = this.quillEditor.root.innerHTML;
                const hidden = document.getElementById('body-hidden');
                if (hidden) { hidden.value = this.form.bodyHtml; }
            });
        },

        autoGenerateSlug() {
            if (!this.slugManuallyEdited) {
                this.form.slug = this.slugify(this.form.title);
            }
        },

        slugify(str) {
            return str.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        },

        expandAllSections()   { Object.keys(this.sections).forEach(k => this.sections[k] = true); },
        collapseAllSections() { Object.keys(this.sections).forEach(k => this.sections[k] = false); },

        addTag() {
            const tag = this.tagInput.trim().replace(/,$/, '');
            if (tag && !this.form.tags.includes(tag)) {
                this.form.tags.push(tag);
            }
            this.tagInput = '';
        },

        removeTag(idx) {
            this.form.tags.splice(idx, 1);
        },

        openMediaLibrary(target) {
            this.mediaTarget = target;
            window.dispatchEvent(new CustomEvent('open-media-picker', {
                detail: { mode: 'single' }
            }));
        },

        handleMediaSelected(detail) {
            const items = detail?.items;
            const url = items?.[0]?.url || items?.[0]?.path || '';
            if (!url) { return; }
            if (this.mediaTarget === 'featured') {
                this.form.featuredImageUrl = url;
            } else if (this.mediaTarget === 'og') {
                this.form.ogImage = url;
            }
        },

        insertCta() {
            const { text, url } = this.ctaModal;
            if (!text.trim() || !url.trim()) { return; }
            if (!this.quillEditor) { return; }
            const range = this.quillEditor.getSelection(true);
            const index = range ? range.index : this.quillEditor.getLength();
            this.quillEditor.clipboard.dangerouslyPasteHTML(
                index,
                `<p><a href="${url}" class="cta-btn">${text} →</a></p><p><br></p>`
            );
            this.form.body = this.quillEditor.getText();
            this.form.bodyHtml = this.quillEditor.root.innerHTML;
            this.modals.cta = false;
        },

        openCategoryModal() {
            this.categoryModal = { name: '', slug: '', slugEdited: false, description: '', isActive: true };
            this.modals.category = true;
        },

        async createCategory(createAnother) {
            const { name, slug, description, isActive } = this.categoryModal;
            if (!name.trim() || !slug.trim()) {
                alert('Category name and slug are required.');
                return;
            }
            if (this.categories.find(c => c.slug === slug)) {
                alert('A category with this slug already exists.');
                return;
            }
            try {
                const newCat = await this.$wire.storeCategory({
                    name: name.trim(),
                    slug,
                    description,
                    is_active: isActive,
                });
                this.categories.push(newCat);
                this.form.categoryId = newCat.id;
                if (createAnother) {
                    this.categoryModal = { name: '', slug: '', slugEdited: false, description: '', isActive: true };
                } else {
                    this.modals.category = false;
                }
                this.$nextTick(() => { if (!this.quillEditor) { this.initQuill(); } });
            } catch (e) {
                alert('Failed to create category: ' + (e.message || e));
            }
        },

        async save(status) {
            if (!this.form.title.trim()) {
                alert('Post title is required.');
                return;
            }
            if (status === 'scheduled' && !this.form.scheduledAt) {
                alert('Please set a schedule date and time.');
                return;
            }
            this.saving = true;
            this.saveAction = status;
            try {
                const result = await this.$wire.savePost(this.form, status);
                if (result.success) {
                    this.form.id = result.id;
                    this.form.slug = result.slug;
                    this.form.status = result.status;
                    // Update page URL to edit URL if newly created
                    if (window.history && !window.location.pathname.includes('/edit')) {
                        const editUrl = '{{ route("admin.blog.index") }}'.replace(/\/blog$/, '/blog/' + result.id + '/edit');
                        window.history.replaceState({}, '', editUrl);
                    }
                } else {
                    alert('Error: ' + (result.error || 'Unknown error'));
                }
            } catch (e) {
                alert('Save failed: ' + (e.message || e));
            } finally {
                this.saving = false;
                this.saveAction = '';
            }
        },
    };
}
</script>
@endpush
