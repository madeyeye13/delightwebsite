<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Under Maintenance — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 dark:bg-[#0f1117] flex items-center justify-center p-6">

    <div class="w-full max-w-lg text-center">

        {{-- Icon --}}
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-amber-500/10 mb-8 mx-auto">
            <svg class="w-10 h-10 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"/>
            </svg>
        </div>

        {{-- Logo / name --}}
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-500 mb-3">{{ config('app.name') }}</p>

        {{-- Heading --}}
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
            We'll be back soon
        </h1>

        {{-- Description --}}
        <p class="text-base text-gray-500 dark:text-white/50 leading-relaxed mb-8">
            We're performing scheduled maintenance to improve your shopping experience.
            Please check back shortly — we won't be long.
        </p>

        {{-- Divider --}}
        <div class="flex items-center gap-3 my-8 max-w-xs mx-auto">
            <div class="flex-1 h-px bg-gray-200 dark:bg-white/[0.08]"></div>
            <span class="text-xs text-gray-400 dark:text-white/30">In the meantime</span>
            <div class="flex-1 h-px bg-gray-200 dark:bg-white/[0.08]"></div>
        </div>

        {{-- Contact hint --}}
        @php $storeEmail = \App\Models\AppSetting::get('store_email', config('mail.from.address')); @endphp
        @if($storeEmail)
        <p class="text-sm text-gray-500 dark:text-white/40">
            Questions? Email us at
            <a href="mailto:{{ $storeEmail }}"
               class="text-amber-500 hover:text-amber-400 font-medium transition-colors">{{ $storeEmail }}</a>
        </p>
        @endif

    </div>

</body>
</html>
