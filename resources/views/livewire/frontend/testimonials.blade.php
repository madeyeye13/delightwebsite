{{--
╔══════════════════════════════════════════════════════════════════╗
║  LIVEWIRE: FRONTEND TESTIMONIALS                                  ║
║  Displays approved testimonials + submit review form              ║
╚══════════════════════════════════════════════════════════════════╝
--}}
<div
    x-data="{
        active: 0,
        timer: null,
        count: {{ $testimonials->count() }},
        showForm: false,
        start() { if (this.count > 1) { this.timer = setInterval(() => this.next(), 5000) } },
        pause() { clearInterval(this.timer) },
        resume() { this.start() },
        next() { this.active = (this.active + 1) % this.count },
        prev() { this.active = (this.active - 1 + this.count) % this.count },
    }"
    x-init="start()"
    @mouseenter="pause()"
    @mouseleave="resume()"
    @keydown.escape.window="showForm = false"
>
    @if($testimonials->isEmpty())
        {{-- Empty state: still show the carousel shell with placeholder --}}
        <div class="tst-inner">
            <div class="tst-heading" aria-hidden="true">
                <span class="tst-heading-line"></span>
                What Our Customers Say
                <span class="tst-heading-line"></span>
            </div>

            <div class="tst-stage" style="min-height:80px">
                <blockquote class="tst-quote" style="position:relative;width:100%;text-align:center">
                    <span class="tst-quote-mark" aria-hidden="true">"</span>Be the first to share your experience with us."
                </blockquote>
            </div>

            <div class="tst-review-wrap" style="margin-top:32px">
                <button
                    class="tst-review-btn"
                    @click="showForm = true"
                    type="button"
                    aria-haspopup="dialog"
                >
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                        <path d="M6.5 1v11M1 6.5h11" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                    </svg>
                    Write a Review
                </button>
            </div>
        </div>
    @else
        <div class="tst-inner">
            {{-- Heading --}}
            <div class="tst-heading" aria-hidden="true">
                <span class="tst-heading-line"></span>
                What Our Customers Say
                <span class="tst-heading-line"></span>
            </div>

            {{-- Quote stage --}}
            <div class="tst-stage" role="region" aria-live="polite" aria-label="Customer testimonial">

                @if($testimonials->count() > 1)
                    <button
                        class="tst-arrow tst-arrow-prev"
                        @click.stop="prev()"
                        aria-label="Previous testimonial"
                        type="button"
                    >
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                            <path d="M9 2L4 7l5 5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                @endif

                @foreach($testimonials as $i => $testimonial)
                    <blockquote
                        class="tst-quote"
                        x-show="active === {{ $i }}"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        style="display:none"
                    >
                        <span class="tst-quote-mark" aria-hidden="true">"</span>{{ $testimonial->quote }}"
                    </blockquote>
                @endforeach

                @if($testimonials->count() > 1)
                    <button
                        class="tst-arrow tst-arrow-next"
                        @click.stop="next()"
                        aria-label="Next testimonial"
                        type="button"
                    >
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                            <path d="M5 2l5 5-5 5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                @endif

            </div>

            {{-- Attribution --}}
            <div class="tst-attribution" aria-live="polite">
                @foreach($testimonials as $i => $testimonial)
                    <div x-show="active === {{ $i }}" style="display:none">
                        <span class="tst-name">{{ $testimonial->name }}</span>
                        @if($testimonial->location)
                            <span class="tst-location">{{ $testimonial->location }}</span>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($testimonials->count() > 1)
                {{-- Dots --}}
                <div class="tst-dots" role="tablist" aria-label="Select testimonial">
                    @foreach($testimonials as $i => $testimonial)
                        <button
                            class="tst-dot"
                            role="tab"
                            :aria-selected="active === {{ $i }}"
                            aria-label="Testimonial {{ $i + 1 }}"
                            @click="active = {{ $i }}"
                            type="button"
                        ></button>
                    @endforeach
                </div>
            @endif

            {{-- Write a review --}}
            <div class="tst-review-wrap">
                <button
                    class="tst-review-btn"
                    @click="showForm = true"
                    type="button"
                    aria-haspopup="dialog"
                >
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                        <path d="M6.5 1v11M1 6.5h11" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                    </svg>
                    Write a Review
                </button>
            </div>

        </div>
    @endif

    {{-- ── Review Modal ── --}}
    <div
        class="tst-modal-backdrop"
        x-show="showForm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.self="showForm = false"
        role="dialog"
        aria-modal="true"
        aria-labelledby="review-modal-title"
        x-cloak
    >
        <div
            class="tst-modal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            {{-- Close --}}
            <button
                class="tst-modal-close"
                @click="showForm = false"
                aria-label="Close review form"
                type="button"
            >
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M2 2l12 12M14 2L2 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>

            @if($submitted)
                <div style="text-align:center;padding:24px 0">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#1F6F67" stroke-width="1.8" style="margin:0 auto 16px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <p class="tst-modal-title" style="margin-bottom:8px">Thank You!</p>
                    <p class="tst-modal-sub" style="margin-bottom:0">Your review has been submitted and will go live after approval.</p>
                </div>
            @else
                <p class="tst-modal-title" id="review-modal-title">Share Your Experience</p>
                <p class="tst-modal-sub">Your feedback helps others shop with confidence.</p>

                {{-- Stars --}}
                <div
                    x-data="{ hovered: 0 }"
                    class="tst-stars"
                    aria-label="Rating (optional)"
                >
                    <template x-for="n in 5" :key="n">
                        <button
                            class="tst-star"
                            :class="{ 'active': n <= (hovered || {{ $rating ?? 0 }}) }"
                            @click="$wire.rating = n"
                            @mouseenter="hovered = n"
                            @mouseleave="hovered = 0"
                            :aria-label="n + ' star' + (n > 1 ? 's' : '')"
                            type="button"
                        >★</button>
                    </template>
                </div>

                @error('rating') <p style="color:#dc2626;font-size:11px;margin-bottom:8px">{{ $message }}</p> @enderror

                <div class="tst-field">
                    <label class="tst-label" for="tst-name">Your Name</label>
                    <input
                        class="tst-input @error('name') !border-red-400 @enderror"
                        id="tst-name"
                        type="text"
                        wire:model="name"
                        placeholder="e.g. Adaeze Okonkwo"
                        autocomplete="name"
                    >
                    @error('name') <p style="color:#dc2626;font-size:11px;margin-top:4px">{{ $message }}</p> @enderror
                </div>

                <div class="tst-field">
                    <label class="tst-label" for="tst-location">City <span style="font-weight:400;opacity:.6">(optional)</span></label>
                    <input
                        class="tst-input"
                        id="tst-location"
                        type="text"
                        wire:model="location"
                        placeholder="e.g. Lagos"
                    >
                </div>

                <div class="tst-field">
                    <label class="tst-label" for="tst-body">Your Review</label>
                    <textarea
                        class="tst-textarea @error('quote') !border-red-400 @enderror"
                        id="tst-body"
                        wire:model="quote"
                        placeholder="Tell us what you bought and what you thought of it…"
                    ></textarea>
                    @error('quote') <p style="color:#dc2626;font-size:11px;margin-top:4px">{{ $message }}</p> @enderror
                </div>

                <div class="tst-modal-footer">
                    <p class="tst-modal-note">Reviews are verified before<br>they go live on the site.</p>
                    <button
                        class="tst-submit-btn"
                        wire:click="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        type="button"
                    >
                        <span wire:loading.remove wire:target="submit">Submit Review</span>
                        <span wire:loading wire:target="submit">Submitting…</span>
                    </button>
                </div>
            @endif

        </div>
    </div>

</div>
