@extends('layouts.custom')

@php
    $alwaysShowHeaderBg = true;
@endphp

@section('title', 'FAQs — Fabric Orders, Shipping & Store Info | 1st Delightsome Fabrics')

@push('head')
    <meta name="description" content="Answers to common questions about buying fabric online from 1st Delightsome Fabrics in Ikeja, Lagos. Learn about fabric types, minimum cuts, delivery timelines, returns, and our walk-in store.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/faq') }}">
    <meta property="og:title" content="FAQs — 1st Delightsome Fabrics">
    <meta property="og:description" content="Everything you need to know before ordering fabric from 1st Delightsome Fabrics.">
    <meta property="og:url" content="{{ url('/faq') }}">
    <meta property="og:type" content="website">
@endpush

@section('content')

{{-- ── Alpine store init: theme (dark mode) ───────────────────────────────── --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('theme', {
            dark: localStorage.getItem('theme') === 'dark'
                || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
            toggle() {
                this.dark = !this.dark;
                localStorage.setItem('theme', this.dark ? 'dark' : 'light');
            }
        });
    });
    // Apply dark class immediately to avoid flash of unstyled content
    (function() {
        var saved = localStorage.getItem('theme');
        if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>

<div class="bg-white dark:bg-ink min-h-screen border-t border-neutral-200 dark:border-neutral-800 pt-40">

    {{-- ── PAGE HERO ── --}}
    <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-16 pt-20 pb-10 border-b border-neutral-200 dark:border-neutral-800">
        <nav class="flex items-center gap-1.5 mb-5 text-xs text-neutral-400" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:text-brand transition-colors duration-150">Home</a>
            <span aria-hidden="true">/</span>
            <span class="text-neutral-600 dark:text-neutral-300">FAQs</span>
        </nav>
        <div class="flex items-center gap-2.5 mb-3">
            <span class="block w-5 h-px bg-accent"></span>
            <span class="text-2xs font-semibold tracking-[0.15em] uppercase text-accent">Got Questions</span>
        </div>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-ink dark:text-neutral-50 tracking-tight mb-2">
            Frequently Asked Questions
        </h1>
        <p class="text-sm text-neutral-500 dark:text-neutral-400 max-w-lg leading-relaxed">
            Everything you need to know before placing your fabric order. Browse the sections below or reach out if something is missing.
        </p>
    </div>

    {{-- ── FAQ CONTENT ── --}}
    <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-16 py-14 space-y-16">

        {{-- ── SECTION 1: Ordering & Products ── --}}
        <section>
            <h2 class="font-display text-lg md:text-xl font-semibold text-ink dark:text-neutral-100 tracking-snug mb-8">
                Ordering &amp; Products
            </h2>

            <div>
                {{-- Q1 --}}
                <div x-data="{ open: false }" class="border-t border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            What kinds of fabric do you sell?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            We stock a wide range of African and premium fabrics including genuine Lace, Aso-oke, Ankara and Kente prints, George fabric, Adire, plain and solid textiles, bridal fabrics, chiffon, and assorted ready-to-sew materials. Our collection is updated regularly to reflect seasonal trends and new arrivals from trusted suppliers.
                        </p>
                    </div>
                </div>

                {{-- Q2 --}}
                <div x-data="{ open: false }" class="border-t border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            What is the minimum amount of fabric I can purchase?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            For most products, the minimum purchase is 1 yard (approximately 0.9 metres). Certain specialty fabrics like Aso-oke are sold per piece or per set, and those details are clearly stated on each product listing. If you need a specific quantity, feel free to contact us before placing your order.
                        </p>
                    </div>
                </div>

                {{-- Q3 --}}
                <div x-data="{ open: false }" class="border-t border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            Can I request a fabric sample before ordering?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            We do not currently offer postal fabric samples. Customers who visit our Ikeja store are welcome to inspect and feel our fabrics in person before making a purchase. We always recommend a visit if you are ordering a large quantity or selecting fabrics for a special occasion.
                        </p>
                    </div>
                </div>

                {{-- Q4 --}}
                <div x-data="{ open: false }" class="border-t border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            How do I know how much fabric I need?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            As a general guide, a blouse or top typically requires 2 to 3 yards, a skirt 2 to 2.5 yards, a full dress 3.5 to 5 yards, and a complete agbada or buba set 10 to 14 yards depending on the style. For bridal or heavily embellished designs, we recommend allowing extra. When in doubt, consult your tailor before placing your order and we are happy to advise if you call or message us.
                        </p>
                    </div>
                </div>

                {{-- Q5 --}}
                <div x-data="{ open: false }" class="border-t border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            Do you accept bulk or wholesale orders?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Yes. We serve both retail and wholesale customers. If you are purchasing for a fashion business, a large event, or require significant yardage, kindly contact us by phone or email so we can provide dedicated pricing and confirm stock availability before you order.
                        </p>
                    </div>
                </div>

                {{-- Q6 --}}
                <div x-data="{ open: false }" class="border-t border-b border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            How do I confirm my order has gone through?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Once payment is completed on our website, you will be taken to a confirmation page and receive an order confirmation email with your order details. If you ordered via phone or message, confirmation is sent once payment is received and acknowledged. Please check your spam folder if the email does not arrive within a few minutes.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── SECTION 2: Shipping & Delivery ── --}}
        <section>
            <h2 class="font-display text-lg md:text-xl font-semibold text-ink dark:text-neutral-100 tracking-snug mb-8">
                Shipping &amp; Delivery
            </h2>

            <div>
                {{-- Q7 --}}
                <div x-data="{ open: false }" class="border-t border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            Do you deliver within Nigeria and internationally?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Yes. We deliver to all states across Nigeria and also ship to international destinations. Delivery fees are calculated at checkout based on your location and the total weight of your order. For international shipments, any applicable customs duties or import taxes at the destination are the responsibility of the buyer.
                        </p>
                    </div>
                </div>

                {{-- Q8 --}}
                <div x-data="{ open: false }" class="border-t border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            How long does delivery take?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Orders within Lagos are typically delivered within 1 to 3 working days. Deliveries to other states in Nigeria usually take 3 to 7 working days. For international orders, timelines vary by destination and carrier; we will confirm the expected delivery window once your order is dispatched.
                        </p>
                    </div>
                </div>

                {{-- Q9 --}}
                <div x-data="{ open: false }" class="border-t border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            How is my fabric packaged for shipping?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            All fabric is neatly folded, wrapped in protective material, and sealed securely before dispatch. Delicate fabrics such as lace, George, and Aso-oke receive additional wrapping to protect against moisture and handling during transit. We take packaging seriously because we understand the value of the fabrics inside.
                        </p>
                    </div>
                </div>

                {{-- Q10 --}}
                <div x-data="{ open: false }" class="border-t border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            Are delivery fees included in the product price?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            No. Product prices shown on our website do not include delivery. Shipping costs are calculated separately during checkout based on your delivery address and order weight.
                        </p>
                    </div>
                </div>

                {{-- Q11 --}}
                <div x-data="{ open: false }" class="border-t border-b border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            What is your return or exchange policy?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Fabric that has been cut, washed, or altered cannot be returned or exchanged under any circumstance. If you receive an item that is faulty or different from what you ordered, you must notify us within 24 hours of delivery with clear photographic evidence. We will assess the claim and arrange a replacement or refund at our discretion. Full details are available on our <a href="{{ route('return-policy') }}" class="text-brand hover:text-brand-600 underline underline-offset-2 transition-colors duration-150">Return Policy page</a>.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── SECTION 3: Fabric Quality & Care ── --}}
        <section>
            <h2 class="font-display text-lg md:text-xl font-semibold text-ink dark:text-neutral-100 tracking-snug mb-8">
                Fabric Quality &amp; Care
            </h2>

            <div>
                {{-- Q12 --}}
                <div x-data="{ open: false }" class="border-t border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            Are fabric colours accurate to the images on your website?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            We photograph our fabrics carefully to represent colours as accurately as possible. That said, slight variations can occur due to differences in screen calibration, lighting conditions during photography, or natural dye lot variations between fabric batches from the manufacturer. If colour accuracy is particularly important to you, we recommend visiting our store in Ikeja to view the fabric directly.
                        </p>
                    </div>
                </div>

                {{-- Q13 --}}
                <div x-data="{ open: false }" class="border-t border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            Do I need to prewash fabric before sewing?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            For cotton-based fabrics such as Ankara and Adire, it is generally good practice to prewash before cutting to account for any natural shrinkage. Lace, Aso-oke, George fabric, and delicate woven fabrics should not be machine washed and are best handled by a dry cleaner or gently hand-washed in cold water if necessary. When in doubt, follow your tailor's guidance.
                        </p>
                    </div>
                </div>

                {{-- Q14 --}}
                <div x-data="{ open: false }" class="border-t border-b border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            How should I store my fabric after purchase?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Store fabric in a cool, dry place away from direct sunlight. Prolonged exposure to sunlight can cause colours to fade over time, particularly on dyed cottons and prints. Delicate fabrics like lace should be stored flat or loosely rolled rather than tightly folded to prevent permanent crease marks. Keeping fabric in a breathable bag or wrapped in cloth will help preserve it.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── SECTION 4: Payments ── --}}
        <section>
            <h2 class="font-display text-lg md:text-xl font-semibold text-ink dark:text-neutral-100 tracking-snug mb-8">
                Payments &amp; Pricing
            </h2>

            <div>
                {{-- Q15 --}}
                <div x-data="{ open: false }" class="border-t border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            What payment methods do you accept?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            We accept debit and credit card payments, bank transfers, and international card payments through our secure online checkout. All transactions are processed through trusted payment providers. Payment details are encrypted and we do not store your card information on our end.
                        </p>
                    </div>
                </div>

                {{-- Q16 --}}
                <div x-data="{ open: false }" class="border-t border-b border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            Are prices listed per yard or per metre?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Pricing is based on the unit shown on each product listing, which varies between fabric types. Most fabrics are priced per yard. Full sets such as Aso-oke or George are sold per piece or set. The unit of measurement is clearly stated on every product page.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── SECTION 5: Store Location ── --}}
        <section>
            <h2 class="font-display text-lg md:text-xl font-semibold text-ink dark:text-neutral-100 tracking-snug mb-8">
                Our Store
            </h2>

            <div>
                {{-- Q17 --}}
                <div x-data="{ open: false }" class="border-t border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            Do you have a physical walk-in store?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Yes. Our store is located at 30b Opebi Rd, Opebi, Ikeja, Lagos 100281. You are welcome to walk in, browse our full collection, and speak directly with our team. No appointment is needed.
                        </p>
                    </div>
                </div>

                {{-- Q18 --}}
                <div x-data="{ open: false }" class="border-t border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            What are your store opening hours?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Our store is open Monday through Saturday from 9am to 6pm. We are closed on Sundays and public holidays. Walk-in customers do not need a prior appointment.
                        </p>
                    </div>
                </div>

                {{-- Q19 --}}
                <div x-data="{ open: false }" class="border-t border-b border-neutral-200 dark:border-neutral-700">
                    <button @click="open = !open"
                            class="w-full py-5 flex justify-between items-center text-left gap-6 bg-transparent border-none cursor-pointer"
                            :aria-expanded="open">
                        <span class="font-sans text-sm font-medium text-ink dark:text-neutral-100 leading-snug">
                            Do you offer payment on delivery?
                        </span>
                        <span class="shrink-0 relative w-[14px] h-[14px]" aria-hidden="true">
                            <span class="absolute top-1/2 left-0 right-0 block h-px bg-neutral-400 dark:bg-neutral-500 -translate-y-1/2 transition-all duration-200"></span>
                            <span class="absolute left-1/2 top-0 bottom-0 block w-px bg-neutral-400 dark:bg-neutral-500 -translate-x-1/2 transition-all duration-200" :class="open ? 'opacity-0 scale-y-0' : 'opacity-100 scale-y-100'"></span>
                        </span>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none" class="pb-5 pr-8">
                        <p class="font-sans text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            We do not offer payment on delivery for online orders. All orders placed through the website must be paid for at the time of checkout. If you prefer to pay cash, you are welcome to visit our Ikeja store directly to purchase and take your fabric with you on the same day.
                        </p>
                    </div>
                </div>
            </div>
        </section>

    </div>{{-- /content --}}

    {{-- ── CTA ── --}}
    <div class="bg-neutral-50 dark:bg-neutral-900 border-t border-neutral-200 dark:border-neutral-800 py-14 px-5">
        <div class="max-w-xl mx-auto text-center">
            <h2 class="font-display text-lg md:text-xl font-semibold text-ink dark:text-neutral-100 tracking-snug mb-2">
                Still have a question?
            </h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-7 leading-relaxed">
                Our team is happy to help. Reach out and we will get back to you as soon as we can.
            </p>
            <a href="{{ url('/contact') }}"
               class="inline-flex items-center gap-2 px-7 py-3 bg-ink dark:bg-brand text-white text-sm font-medium tracking-wide hover:opacity-80 transition-opacity duration-150">
                Contact Us
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
    </div>

</div>

@endsection
