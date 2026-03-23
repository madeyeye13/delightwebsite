{{--
╔══════════════════════════════════════════════════════════════════╗
║  LIVEWIRE: ADMIN BLOG POST LIST                                   ║
║  Full-page component view — data from BlogIndex::render()         ║
╚══════════════════════════════════════════════════════════════════╝
--}}

<div x-data="blogListManager()" class="space-y-6">

<script>
    window.__adminBlog = {!! $postsJson !!};
    window.__adminBlogMeta = {
        categories: {!! json_encode($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'slug' => $c->slug])->values()) !!},
    };
</script>

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900 dark:text-neutral-50">Blog Posts</h1>
            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">Manage your blog content and article library</p>
        </div>
        <a href="{{ route('admin.blog.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand-600 transition-colors duration-200 font-medium text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Post
        </a>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Total</p>
            <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Published</p>
            <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['published'] }}</p>
        </div>
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Drafts</p>
            <p class="mt-2 text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['drafts'] }}</p>
        </div>
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Scheduled</p>
            <p class="mt-2 text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['scheduled'] }}</p>
        </div>
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
            <p class="text-neutral-600 dark:text-neutral-400 text-xs font-medium">Featured</p>
            <p class="mt-2 text-3xl font-bold text-accent-600 dark:text-accent-400">{{ $stats['featured'] }}</p>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 p-4">
        <div class="mb-4">
            <input
                type="text"
                x-model="search"
                @input="applyFilters()"
                placeholder="Search by title, author, or tag..."
                class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded text-sm text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 placeholder-neutral-500 focus:ring-2 focus:ring-brand focus:border-transparent"
            />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
            {{-- Category --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Category</label>
                <div class="relative">
                    <button @click="open = !open" @click.away="open = false"
                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 flex items-center justify-between">
                        <span x-text="filters.category ? getCategoryLabel(filters.category) : 'All'"></span>
                        <svg class="w-3 h-3 text-neutral-400 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20 max-h-48 overflow-y-auto">
                        <button @click="filters.category = ''; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">All</button>
                        @foreach ($categories as $cat)
                            <button @click="filters.category = '{{ $cat->slug }}'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">{{ $cat->name }}</button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Status</label>
                <div class="relative">
                    <button @click="open = !open" @click.away="open = false"
                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 flex items-center justify-between">
                        <span x-text="!filters.status ? 'All' : filters.status.charAt(0).toUpperCase() + filters.status.slice(1)"></span>
                        <svg class="w-3 h-3 text-neutral-400 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20">
                        <button @click="filters.status = ''; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">All</button>
                        <button @click="filters.status = 'published'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Published</button>
                        <button @click="filters.status = 'draft'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Draft</button>
                        <button @click="filters.status = 'scheduled'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Scheduled</button>
                    </div>
                </div>
            </div>

            {{-- Sort --}}
            <div x-data="{ open: false }">
                <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Sort</label>
                <div class="relative">
                    <button @click="open = !open" @click.away="open = false"
                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 rounded text-left text-xs text-neutral-900 dark:text-neutral-50 dark:bg-neutral-900/50 hover:border-neutral-400 flex items-center justify-between">
                        <span x-text="getSortLabel()"></span>
                        <svg class="w-3 h-3 text-neutral-400 flex-shrink-0" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="absolute top-full left-0 right-0 mt-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded shadow-lg z-20">
                        <button @click="filters.sortBy = 'newest'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Newest</button>
                        <button @click="filters.sortBy = 'oldest'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Oldest</button>
                        <button @click="filters.sortBy = 'title-asc'; open = false; applyFilters()" class="w-full text-left px-2.5 py-1.5 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Title A–Z</button>
                    </div>
                </div>
            </div>

            {{-- Clear --}}
            <div class="flex items-end">
                <button @click="clearFilters()" class="text-brand dark:text-brand-300 hover:text-brand-600 font-medium text-xs">Clear</button>
            </div>
        </div>
    </div>

    {{-- BULK ACTIONS --}}
    <div x-show="selectedRows.length > 0" x-transition
         class="bg-brand-50 dark:bg-brand-900/20 border border-brand-200 dark:border-brand-700/50 rounded-lg p-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div class="text-xs text-brand-900 dark:text-brand-200 font-medium">
            <span x-text="selectedRows.length"></span>
            <span x-text="selectedRows.length === 1 ? ' post selected' : ' posts selected'"></span>
        </div>
        <div class="flex flex-wrap gap-2">
            <button @click="bulkPublish()" class="px-3 py-1.5 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors font-medium">Publish</button>
            <button @click="bulkDraft()" class="px-3 py-1.5 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700 transition-colors font-medium">Draft</button>
            <button @click="bulkDelete()" class="px-3 py-1.5 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors font-medium">Delete</button>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-lg border border-neutral-200 dark:border-neutral-800 overflow-hidden">
        {{-- Empty State --}}
        <div x-show="filteredPosts.length === 0" class="p-12 text-center">
            <svg class="w-12 h-12 text-neutral-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-neutral-600 dark:text-neutral-400 font-medium text-sm" x-text="search || Object.values(filters).some(v => v) ? 'No posts match your filters' : 'No blog posts yet'"></p>
            <a href="{{ route('admin.blog.create') }}" class="mt-3 inline-flex items-center gap-1 text-brand hover:text-brand-600 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create your first post
            </a>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-100 dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-800">
                    <tr>
                        <th class="px-4 py-2.5 text-left w-4">
                            <input type="checkbox" @change="toggleSelectAll()" :checked="selectedRows.length === filteredPosts.length && filteredPosts.length > 0" class="w-4 h-4 rounded border-neutral-300">
                        </th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Post</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Category</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Author</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Featured</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Published</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Updated</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                    <template x-for="post in filteredPosts" :key="post.id">
                        <tr class="hover:bg-neutral-100 dark:hover:bg-neutral-900/50 transition-colors">
                            <td class="px-4 py-2.5">
                                <input type="checkbox" @change="toggleRow(post.id)" :checked="selectedRows.includes(post.id)" class="w-4 h-4 rounded border-neutral-300">
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-2 max-w-xs">
                                    <div class="w-8 h-8 rounded bg-gradient-to-br from-neutral-200 to-neutral-300 dark:from-neutral-800 dark:to-neutral-900 flex-shrink-0 overflow-hidden">
                                        <template x-if="post.featured_image_url">
                                            <img :src="post.featured_image_url" :alt="post.title" class="w-full h-full object-cover">
                                        </template>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-xs text-neutral-900 dark:text-neutral-50 truncate" x-text="post.title"></p>
                                        <p class="text-xs text-neutral-500 truncate" x-text="'/blog/' + post.slug"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 text-xs text-neutral-600 dark:text-neutral-400" x-text="post.categoryLabel"></td>
                            <td class="px-4 py-2.5 text-xs text-neutral-600 dark:text-neutral-400" x-text="post.author"></td>
                            <td class="px-4 py-2.5 text-xs">
                                <template x-if="post.status === 'published'">
                                    <span class="bg-green-50 dark:bg-green-500/20 text-green-700 dark:text-green-300 px-2 py-0.5 rounded font-medium">Published</span>
                                </template>
                                <template x-if="post.status === 'draft'">
                                    <span class="bg-yellow-50 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded font-medium">Draft</span>
                                </template>
                                <template x-if="post.status === 'scheduled'">
                                    <span class="bg-purple-50 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 px-2 py-0.5 rounded font-medium">Scheduled</span>
                                </template>
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <template x-if="post.featured">
                                    <svg class="w-4 h-4 text-accent-500 mx-auto" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </template>
                            </td>
                            <td class="px-4 py-2.5 text-xs text-neutral-500" x-text="post.published_at"></td>
                            <td class="px-4 py-2.5 text-xs text-neutral-500" x-text="post.updated"></td>
                            <td class="px-4 py-2.5 text-center">
                                <div x-data="{ open: false }" class="relative inline-block" @click.away="open = false">
                                    <button @click="open = !open" class="p-1 hover:bg-neutral-200 dark:hover:bg-neutral-800 rounded transition-colors">
                                        <svg class="w-4 h-4 text-neutral-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" class="absolute right-0 mt-1 w-40 bg-neutral-50 dark:bg-neutral-900 rounded-lg shadow-lg border border-neutral-200 dark:border-neutral-700 z-10">
                                        <a :href="'/admin/blog/' + post.id + '/edit'" class="block px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 first:rounded-t-lg border-b border-neutral-200 dark:border-neutral-700">Edit</a>
                                        <a :href="'/blog/' + post.slug" target="_blank" class="block px-3 py-2 text-xs text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700">View</a>
                                        <button @click="open = false; confirmDelete(post.id, post.title)" class="w-full text-left px-3 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-b-lg">Delete</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-neutral-200 dark:divide-neutral-800">
            <template x-for="post in filteredPosts" :key="post.id">
                <div class="p-4 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <input type="checkbox" @change="toggleRow(post.id)" :checked="selectedRows.includes(post.id)" class="w-4 h-4 rounded border-neutral-300 shrink-0">
                            <p class="font-medium text-sm text-neutral-900 dark:text-neutral-50 truncate" x-text="post.title"></p>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <a :href="'/admin/blog/' + post.id + '/edit'" class="text-xs text-brand hover:text-brand-600 font-medium">Edit</a>
                            <button @click="confirmDelete(post.id, post.title)" class="text-xs text-red-600 hover:text-red-700 font-medium">Delete</button>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-neutral-500">
                        <span x-text="post.categoryLabel"></span>
                        <span>·</span>
                        <span x-text="post.author"></span>
                        <span>·</span>
                        <template x-if="post.status === 'published'"><span class="text-green-600 dark:text-green-400">Published</span></template>
                        <template x-if="post.status === 'draft'"><span class="text-yellow-600 dark:text-yellow-400">Draft</span></template>
                        <template x-if="post.status === 'scheduled'"><span class="text-purple-600 dark:text-purple-400">Scheduled</span></template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- DELETE CONFIRMATION MODAL --}}
    <template x-teleport="body">
    <div x-show="confirm.open" x-transition
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4"
         @click.self="confirm.open = false"
         style="display:none">
        <div x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             class="bg-neutral-50 dark:bg-[#1a2332] rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-2xl w-full max-w-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-neutral-900 dark:text-neutral-50 text-sm">Delete Post</h3>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">This action cannot be undone.</p>
                </div>
            </div>
            <p class="text-sm text-neutral-700 dark:text-neutral-300 mb-5">Are you sure you want to delete <strong x-text="confirm.title"></strong>?</p>
            <div class="flex gap-3">
                <button @click="doDelete()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-sm">Delete</button>
                <button @click="confirm.open = false" class="flex-1 px-4 py-2 bg-neutral-200 dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 rounded-lg hover:bg-neutral-300 dark:hover:bg-neutral-700 transition-colors font-medium text-sm">Cancel</button>
            </div>
        </div>
    </div>
    </template>

    {{-- BULK DELETE CONFIRMATION MODAL --}}
    <template x-teleport="body">
    <div x-show="bulkConfirm.open" x-transition
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4"
         @click.self="bulkConfirm.open = false"
         style="display:none">
        <div class="bg-neutral-50 dark:bg-[#1a2332] rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-2xl w-full max-w-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-neutral-900 dark:text-neutral-50 text-sm">Delete Selected Posts</h3>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">This action cannot be undone.</p>
                </div>
            </div>
            <p class="text-sm text-neutral-700 dark:text-neutral-300 mb-5">Delete <strong x-text="selectedRows.length"></strong> selected posts permanently?</p>
            <div class="flex gap-3">
                <button @click="doBulkDelete()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-sm">Delete All</button>
                <button @click="bulkConfirm.open = false" class="flex-1 px-4 py-2 bg-neutral-200 dark:bg-neutral-800 text-neutral-900 dark:text-neutral-50 rounded-lg hover:bg-neutral-300 dark:hover:bg-neutral-700 transition-colors font-medium text-sm">Cancel</button>
            </div>
        </div>
    </div>
    </template>

</div>

<script>
function blogListManager() {
    return {
        allPosts: window.__adminBlog || [],
        filteredPosts: [],
        search: '',
        filters: { category: '', status: '', sortBy: 'newest' },
        selectedRows: [],
        confirm: { open: false, id: null, title: '' },
        bulkConfirm: { open: false },

        init() {
            this.filteredPosts = [...this.allPosts];

            window.addEventListener('post-deleted', e => {
                this.allPosts = this.allPosts.filter(p => p.id !== e.detail.id);
                this.applyFilters();
            });
            window.addEventListener('posts-bulk-deleted', e => {
                this.allPosts = this.allPosts.filter(p => !e.detail.ids.includes(p.id));
                this.selectedRows = [];
                this.applyFilters();
            });
        },

        applyFilters() {
            let result = [...this.allPosts];
            if (this.search) {
                const q = this.search.toLowerCase();
                result = result.filter(p =>
                    p.title.toLowerCase().includes(q) ||
                    p.author.toLowerCase().includes(q) ||
                    p.tags.some(t => t.toLowerCase().includes(q))
                );
            }
            if (this.filters.category) {
                result = result.filter(p => p.categoryKey === this.filters.category);
            }
            if (this.filters.status) {
                result = result.filter(p => p.status === this.filters.status);
            }
            if (this.filters.sortBy === 'oldest') {
                result.reverse();
            } else if (this.filters.sortBy === 'title-asc') {
                result.sort((a, b) => a.title.localeCompare(b.title));
            }
            this.filteredPosts = result;
        },

        clearFilters() {
            this.search = '';
            this.filters = { category: '', status: '', sortBy: 'newest' };
            this.applyFilters();
        },

        getCategoryLabel(slug) {
            const cats = window.__adminBlogMeta?.categories || [];
            const match = cats.find(c => c.slug === slug);
            return match ? match.name : slug;
        },

        getSortLabel() {
            const map = { newest: 'Newest', oldest: 'Oldest', 'title-asc': 'Title A–Z' };
            return map[this.filters.sortBy] || 'Newest';
        },

        toggleSelectAll() {
            if (this.selectedRows.length === this.filteredPosts.length) {
                this.selectedRows = [];
            } else {
                this.selectedRows = this.filteredPosts.map(p => p.id);
            }
        },

        toggleRow(id) {
            if (this.selectedRows.includes(id)) {
                this.selectedRows = this.selectedRows.filter(r => r !== id);
            } else {
                this.selectedRows.push(id);
            }
        },

        confirmDelete(id, title) {
            this.confirm = { open: true, id, title };
        },

        doDelete() {
            const id = this.confirm.id;
            this.confirm.open = false;
            this.$wire.deletePost(id);
        },

        bulkDelete() {
            this.bulkConfirm.open = true;
        },

        doBulkDelete() {
            const ids = [...this.selectedRows];
            this.bulkConfirm.open = false;
            this.$wire.bulkDelete(ids);
        },

        bulkPublish() {
            const ids = [...this.selectedRows];
            ids.forEach(id => this.$wire.updateStatus(id, 'published'));
            ids.forEach(id => {
                const p = this.allPosts.find(p => p.id === id);
                if (p) { p.status = 'published'; }
            });
            this.applyFilters();
        },

        bulkDraft() {
            const ids = [...this.selectedRows];
            ids.forEach(id => this.$wire.updateStatus(id, 'draft'));
            ids.forEach(id => {
                const p = this.allPosts.find(p => p.id === id);
                if (p) { p.status = 'draft'; }
            });
            this.applyFilters();
        },
    };
}
</script>
