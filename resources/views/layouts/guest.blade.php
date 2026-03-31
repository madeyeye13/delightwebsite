@props(['image' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1F6F67">
    <meta name="author" content="Bezalel Koncept">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" type="image/png" href="/images/logo1.png">
    <link rel="apple-touch-icon" href="/images/logo1.png">

    {{-- Fonts matching your Tailwind config --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ─────────────────────────────────────────────────────
           AUTOFILL FIX
           Chrome/Safari inject a blue/yellow background on
           autofilled inputs. This overrides it to match your
           panel background (ink-soft = #1A1D20).
        ───────────────────────────────────────────────────── */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #1A1D20 inset !important;
            -webkit-text-fill-color: #ffffff !important;
            caret-color: #ffffff;
            /* Delay the transition to prevent a visible flash on page load */
            transition: background-color 9999s ease-in-out 0s;
        }

        /* ─────────────────────────────────────────────────────
           KILL ALL FOCUS RINGS
           The @tailwindcss/forms plugin adds focus rings.
           These rules remove every visible focus indicator
           from inputs and buttons (we use a custom underline
           animation instead).
        ───────────────────────────────────────────────────── */
        input:focus,
        input:focus-visible,
        textarea:focus,
        select:focus,
        button:focus,
        button:focus-visible {
            outline: none !important;
            box-shadow: none !important;
            --tw-ring-shadow: 0 0 0 0 transparent !important;
            --tw-ring-color: transparent !important;
            border-color: rgba(255,255,255,0.25) !important;
        }

        /* ─────────────────────────────────────────────────────
           ANIMATED UNDERLINE ON FOCUS
           A white line slides in under the input on focus.
        ───────────────────────────────────────────────────── */
        .input-wrap {
            position: relative;
        }
        .input-wrap::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 1px;
            background: #ffffff;
            transition: width 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .input-wrap:focus-within::after {
            width: 100%;
        }

        /* ─────────────────────────────────────────────────────
           PAGE ANIMATIONS
        ───────────────────────────────────────────────────── */
        @keyframes panelSlideIn {
            from { opacity: 0; transform: translateX(-22px); }
            to   { opacity: 1; transform: translateX(0);     }
        }
        @keyframes imageReveal {
            from { opacity: 0; transform: scale(1.04); }
            to   { opacity: 1; transform: scale(1);    }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0);    }
        }

        .anim-panel   { animation: panelSlideIn 0.75s cubic-bezier(0.22,1,0.36,1) both; }
        .anim-image   { animation: imageReveal  1.0s  cubic-bezier(0.22,1,0.36,1) both 0.1s; }
        .anim-heading { animation: fadeUp 0.6s cubic-bezier(0.22,1,0.36,1) both 0.20s; }
        .anim-sub     { animation: fadeUp 0.6s cubic-bezier(0.22,1,0.36,1) both 0.28s; }
        .anim-actions { opacity:0; animation: fadeUp 0.6s cubic-bezier(0.22,1,0.36,1) forwards 0.58s; }
        .anim-footer  { opacity:0; animation: fadeUp 0.6s cubic-bezier(0.22,1,0.36,1) forwards 0.65s; }

        /* Staggered field entrance */
        .anim-field { opacity: 0; animation: fadeUp 0.6s cubic-bezier(0.22,1,0.36,1) forwards; }
        .anim-field:nth-child(1) { animation-delay: 0.30s; }
        .anim-field:nth-child(2) { animation-delay: 0.38s; }
        .anim-field:nth-child(3) { animation-delay: 0.46s; }
        .anim-field:nth-child(4) { animation-delay: 0.54s; }
        .anim-field:nth-child(5) { animation-delay: 0.62s; }
    </style>
</head>
<body class="bg-ink font-sans min-h-screen">

<div class="flex min-h-screen">

    {{-- ── Left: Form Panel ── --}}
    <div class="anim-panel
                w-full
                lg:w-[44%] lg:min-w-[480px]
                xl:min-w-[520px]
                bg-ink-soft
                flex flex-col
                px-6 py-8
                sm:px-10
                md:px-14
                lg:px-16
                xl:px-20">

        {{-- Back to Home --}}
        <div>
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2.5
                      text-neutral-400 hover:text-white
                      text-2xs font-medium tracking-widest uppercase
                      transition-colors duration-200
                      group">
                <svg width="18" height="10" viewBox="0 0 18 10" fill="none"
                     class="transition-transform duration-200 group-hover:-translate-x-1">
                    <path d="M0 5H17M6 1L1 5L6 9"
                          stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/>
                </svg>
                Back to Home
            </a>
        </div>

        {{-- Slot (form content) --}}
        <div class="flex-1 flex flex-col justify-center py-10">
            {{ $slot }}
        </div>

    </div>

    {{-- ── Right: Image Panel ── --}}
    <div class="anim-image hidden lg:block flex-1 relative overflow-hidden"
         style="background: linear-gradient(135deg, #1c1007 0%, #2e1c0c 60%, #1a0f05 100%);">
        @if($image)
            <img src="{{ $image }}" alt=""
                 class="absolute inset-0 w-full h-full object-cover object-center" />
            {{-- Gradient bleed from left panel --}}
            <div class="absolute inset-0 pointer-events-none"
                 style="background: linear-gradient(to right, rgba(26,13,5,0.35) 0%, transparent 35%);"></div>
        @endif
    </div>

</div>

<script>
    document.querySelectorAll('[data-pw-toggle]').forEach(btn => {
        btn.addEventListener('click', () => {
            const wrap  = btn.closest('.input-wrap');
            const input = wrap.querySelector('input');
            const isHidden = input.type === 'password';
            input.type     = isHidden ? 'text' : 'password';
            btn.textContent = isHidden ? 'Hide' : 'Show';
        });
    });
</script>

</body>
</html>