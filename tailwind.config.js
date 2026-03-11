import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {

            // ─────────────────────────────────────────────────
            // FONTS
            // font-sans   → Plus Jakarta Sans  (body copy, UI)
            // font-display → Manrope            (headings, brand)
            // ─────────────────────────────────────────────────
            fontFamily: {
                sans:    ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                display: ['Manrope',           ...defaultTheme.fontFamily.sans],
            },

            // ─────────────────────────────────────────────────
            // FONT SIZE SCALE
            // Format: [font-size, { lineHeight, letterSpacing, fontWeight }]
            //
            // Naming convention:
            //   text-2xs   →  10px   micro labels, badges
            //   text-xs    →  12px   captions, helper text        (Tailwind default kept)
            //   text-sm    →  13px   secondary body, table cells  (Tailwind default tightened)
            //   text-base  →  15px   primary body copy            (bumped from 16px for density)
            //   text-md    →  16px   slightly larger body / card titles
            //   text-lg    →  18px   section subheadings
            //   text-xl    →  20px   page subheadings
            //   text-2xl   →  24px   page titles (mobile)
            //   text-3xl   →  28px   page titles (desktop)
            //   text-4xl   →  34px   hero / dashboard stat numbers
            //   text-5xl   →  42px   large display numbers
            // ─────────────────────────────────────────────────
            fontSize: {
                '2xs':  ['10px', { lineHeight: '14px', letterSpacing: '0.02em' }],
                'xs':   ['12px', { lineHeight: '16px', letterSpacing: '0.01em' }],
                'sm':   ['13px', { lineHeight: '20px', letterSpacing: '0em'    }],
                'base': ['15px', { lineHeight: '24px', letterSpacing: '0em'    }],
                'md':   ['16px', { lineHeight: '24px', letterSpacing: '-0.01em'}],
                'lg':   ['18px', { lineHeight: '28px', letterSpacing: '-0.01em'}],
                'xl':   ['20px', { lineHeight: '30px', letterSpacing: '-0.02em'}],
                '2xl':  ['24px', { lineHeight: '32px', letterSpacing: '-0.02em'}],
                '3xl':  ['28px', { lineHeight: '36px', letterSpacing: '-0.03em'}],
                '4xl':  ['34px', { lineHeight: '42px', letterSpacing: '-0.03em'}],
                '5xl':  ['42px', { lineHeight: '52px', letterSpacing: '-0.04em'}],
            },

            // ─────────────────────────────────────────────────
            // FONT WEIGHT
            // Use these semantic aliases in your components:
            //
            //   font-light    → 300   fine print, de-emphasized
            //   font-normal   → 400   body copy
            //   font-medium   → 500   labels, nav items, buttons
            //   font-semibold → 600   subheadings, card titles
            //   font-bold     → 700   headings, emphasis
            //   font-extrabold→ 800   hero text, stat numbers (Manrope only)
            //
            // Tailwind already ships these — no override needed.
            // Documented here for team reference.
            // ─────────────────────────────────────────────────

            // ─────────────────────────────────────────────────
            // LINE HEIGHT (named scale for prose control)
            //
            //   leading-none    → 1       stat numbers, single-line display
            //   leading-tight   → 1.2     headings
            //   leading-snug    → 1.35    subheadings
            //   leading-normal  → 1.5     body copy
            //   leading-relaxed → 1.65    long-form text, descriptions
            //   leading-loose   → 2       spaced lists
            //
            // Tailwind already ships these — documented for reference.
            // ─────────────────────────────────────────────────
            lineHeight: {
                'none':     '1',
                'tight':    '1.2',
                'snug':     '1.35',
                'normal':   '1.5',
                'relaxed':  '1.65',
                'loose':    '2',
            },

            // ─────────────────────────────────────────────────
            // LETTER SPACING
            //
            //   tracking-tighter → -0.04em   large display headings
            //   tracking-tight   → -0.02em   section headings
            //   tracking-snug    → -0.01em   card titles
            //   tracking-normal  →  0em      body
            //   tracking-wide    →  0.02em   labels, small caps
            //   tracking-wider   →  0.05em   uppercase badges, nav labels
            //   tracking-widest  →  0.1em    all-caps micro labels
            // ─────────────────────────────────────────────────
            letterSpacing: {
                'tighter': '-0.04em',
                'tight':   '-0.02em',
                'snug':    '-0.01em',
                'normal':  '0em',
                'wide':    '0.02em',
                'wider':   '0.05em',
                'widest':  '0.1em',
            },
        },
    },

    plugins: [forms],
};

// ═══════════════════════════════════════════════════════════════════
// USAGE GUIDE — copy this comment block into your project docs
// ═══════════════════════════════════════════════════════════════════
//
// HEADINGS (use font-display = Manrope)
//   Page title (desktop):   font-display text-3xl font-bold    leading-tight  tracking-tight
//   Page title (mobile):    font-display text-2xl font-bold    leading-tight  tracking-tight
//   Section heading:        font-display text-xl  font-semibold leading-snug  tracking-snug
//   Card title:             font-display text-md  font-semibold leading-snug
//   Stat number:            font-display text-4xl font-extrabold leading-none tracking-tighter
//
// BODY (use font-sans = Plus Jakarta Sans)
//   Primary body copy:      font-sans text-base font-normal  leading-normal
//   Secondary / helper:     font-sans text-sm   font-normal  leading-normal  text-gray-500
//   Table cell:             font-sans text-sm   font-medium
//   Label / form:           font-sans text-sm   font-medium  leading-none
//   Caption / timestamp:    font-sans text-xs   font-normal  text-gray-400
//   Micro badge / tag:      font-sans text-2xs  font-semibold tracking-wide uppercase
//
// BUTTONS
//   Primary:    font-sans text-sm  font-semibold
//   Small:      font-sans text-xs  font-medium
//
// NAV ITEMS
//   Sidebar:    font-sans text-sm  font-medium
//   Section label: font-sans text-2xs font-semibold tracking-widest uppercase
//
// RESPONSIVE PATTERN (always mobile-first)
//   text-2xl md:text-3xl lg:text-4xl  — for page titles
//   text-sm  md:text-base             — for body copy that needs room
//   text-xs  md:text-sm               — for helper/caption text
//
// ═══════════════════════════════════════════════════════════════════