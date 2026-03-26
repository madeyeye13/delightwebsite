<div x-on:search-reset.window="$wire.clearQuery()">

    {{-- ── INPUT ROW ── --}}
    <div class="flex items-center border-b-2 border-black pb-2 gap-3">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 flex-shrink-0">
            <path d="M11.048 17.89a6.923 6.923 0 1 0 0-13.847 6.923 6.923 0 0 0 0 13.847z" stroke="currentColor" stroke-width="1.2"/>
            <path d="m16 16 4.308 4.308" stroke="currentColor" stroke-width="1.2"/>
        </svg>

        <input
            id="global-search-input"
            type="text"
            wire:model.live="query"
            placeholder="Search fabrics, blog posts, categories…"
            class="flex-1 border-none outline-none font-sans text-xl text-black placeholder-gray-300 bg-transparent"
            autocomplete="off"
            spellcheck="false"
        />

        @if(strlen($query) > 0)
        <button
            wire:click="clearQuery"
            class="text-gray-400 hover:text-black transition-colors bg-transparent border-none cursor-pointer flex-shrink-0"
            aria-label="Clear search"
        >
            <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4">
                <path d="M18.462 6.479 5.538 19.402M5.538 6.479l12.924 12.923" stroke="currentColor" stroke-width="1.5"/>
            </svg>
        </button>
        @endif
    </div>

    {{-- ── STATES ── --}}
    @if(strlen(trim($query)) === 0)
        <p class="mt-4 font-sans text-xs text-gray-400">
            Try: "lace fabric", "ankara", "aso-oke"
        </p>

    @elseif(strlen(trim($query)) < 2)
        <p class="mt-4 font-sans text-xs text-gray-400">
            Please enter at least 2 characters…
        </p>

    @elseif(empty($results['products']) && empty($results['posts']) && empty($results['categories']) && empty($results['pages']))
        <p class="mt-4 font-sans text-sm text-gray-500">
            No results for "<span class="text-black font-medium">{{ $query }}</span>"
        </p>

    @else
        {{-- ── RESULTS ── --}}
        <div class="mt-5 space-y-5 max-h-[55vh] overflow-y-auto -mx-3 px-3">

            {{-- Products --}}
            @if(!empty($results['products']))
            <div>
                <h4 class="text-[10px] font-semibold uppercase tracking-[2px] text-gray-400 mb-2 px-1">Products</h4>
                <div class="space-y-0.5">
                    @foreach($results['products'] as $item)
                    <a
                        href="{{ $item['url'] }}"
                        wire:key="product-{{ $loop->index }}"
                        class="flex items-center gap-3 px-2 py-2.5 hover:bg-gray-50 rounded-lg group transition-colors duration-150"
                    >
                        @if($item['image'])
                            <img
                                src="{{ $item['image'] }}"
                                alt="{{ $item['title'] }}"
                                class="w-10 h-10 object-cover rounded flex-shrink-0 bg-gray-100"
                            >
                        @else
                            <div class="w-10 h-10 bg-gray-100 rounded flex-shrink-0 flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 group-hover:text-black truncate">{{ $item['title'] }}</p>
                            @if($item['label'])
                            <p class="text-xs text-gray-400 truncate">{{ $item['label'] }}</p>
                            @endif
                        </div>
                        <span class="text-sm font-medium text-gray-700 flex-shrink-0">{{ format_price($item['price']) }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Blog Posts --}}
            @if(!empty($results['posts']))
            <div>
                <h4 class="text-[10px] font-semibold uppercase tracking-[2px] text-gray-400 mb-2 px-1">Blog</h4>
                <div class="space-y-0.5">
                    @foreach($results['posts'] as $item)
                    <a
                        href="{{ $item['url'] }}"
                        wire:key="post-{{ $loop->index }}"
                        class="flex items-center gap-3 px-2 py-2.5 hover:bg-gray-50 rounded-lg group transition-colors duration-150"
                    >
                        @if($item['image'])
                            <img
                                src="{{ $item['image'] }}"
                                alt="{{ $item['title'] }}"
                                class="w-10 h-10 object-cover rounded flex-shrink-0 bg-gray-100"
                            >
                        @else
                            <div class="w-10 h-10 bg-gray-100 rounded flex-shrink-0 flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 group-hover:text-black truncate">{{ $item['title'] }}</p>
                            @if($item['excerpt'])
                            <p class="text-xs text-gray-400 truncate">{{ \Illuminate\Support\Str::limit($item['excerpt'], 60) }}</p>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Categories --}}
            @if(!empty($results['categories']))
            <div>
                <h4 class="text-[10px] font-semibold uppercase tracking-[2px] text-gray-400 mb-2 px-1">Categories</h4>
                <div class="flex flex-wrap gap-2 px-1">
                    @foreach($results['categories'] as $item)
                    <a
                        href="{{ $item['url'] }}"
                        wire:key="cat-{{ $loop->index }}"
                        class="px-3 py-1.5 text-xs font-medium text-gray-700 border border-gray-200 rounded-full hover:border-black hover:text-black transition-colors duration-150"
                    >
                        {{ $item['title'] }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Pages --}}
            @if(!empty($results['pages']))
            <div>
                <h4 class="text-[10px] font-semibold uppercase tracking-[2px] text-gray-400 mb-2 px-1">Pages</h4>
                <div class="space-y-0.5">
                    @foreach($results['pages'] as $item)
                    <a
                        href="{{ $item['url'] }}"
                        wire:key="page-{{ $loop->index }}"
                        class="flex items-center gap-3 px-2 py-2.5 hover:bg-gray-50 rounded-lg group transition-colors duration-150"
                    >
                        <div class="w-10 h-10 bg-gray-50 border border-gray-100 rounded flex-shrink-0 flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800 group-hover:text-black">{{ $item['title'] }}</p>
                            <p class="text-xs text-gray-400">{{ $item['desc'] }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    @endif

</div>
