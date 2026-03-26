<section class="tst-section" aria-labelledby="tst-heading">
    <style>
        .tst-section {
            background: #FCFCF9;
            padding: 88px 64px;
            border-top: 1px solid #E5E5E5;
            border-bottom: 1px solid #E5E5E5;
        }

        .dark .tst-section,
        [data-theme="dark"] .tst-section {
            background: #071E1E;
            border-color: #134643;
        }

        .tst-inner { max-width: 860px; margin: 0 auto; }

        /* Heading */
        .tst-heading {
            font-family: 'Manrope', sans-serif;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #1F6F67;
            text-align: center;
            margin-bottom: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
        }

        .dark .tst-heading,
        [data-theme="dark"] .tst-heading { color: #33A89F; }

        .tst-heading-line {
            flex: 1;
            max-width: 48px;
            height: 1px;
            background: #1F6F67;
            opacity: 0.35;
        }

        #tst-heading {
            position: absolute;
            width: 1px; height: 1px;
            overflow: hidden;
            clip: rect(0,0,0,0);
            white-space: nowrap;
        }

        /* Stage */
        .tst-stage {
            position: relative;
            min-height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 32px;
            padding: 0 64px;
        }

        .tst-quote {
            font-family: 'Manrope', sans-serif;
            font-size: clamp(17px, 2.2vw, 22px);
            font-weight: 400;
            line-height: 1.7;
            letter-spacing: -0.015em;
            color: #404040;
            text-align: center;
            position: absolute;
            width: calc(100% - 128px);
        }

        .dark .tst-quote,
        [data-theme="dark"] .tst-quote { color: #A3A3A3; }

        .tst-quote-mark {
            font-family: 'Manrope', sans-serif;
            font-size: 52px;
            line-height: 0;
            vertical-align: -20px;
            color: #D9A21B;
            margin-right: 2px;
            font-weight: 700;
        }

        /* Attribution */
        .tst-attribution {
            text-align: center;
            margin-bottom: 36px;
            min-height: 36px;
        }

        .tst-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.03em;
            color: #111315;
            display: block;
            margin-bottom: 2px;
        }

        .dark .tst-name,
        [data-theme="dark"] .tst-name { color: #F9F9F9; }

        .tst-location {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 11px;
            font-weight: 400;
            color: #A3A3A3;
        }

        /* Dots */
        .tst-dots {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 40px;
        }

        .tst-dot {
            width: 5px;
            height: 5px;
            background: #E5E5E5;
            border: none;
            cursor: pointer;
            padding: 0;
            transition: background 0.3s, width 0.3s;
        }

        .dark .tst-dot,
        [data-theme="dark"] .tst-dot { background: #134643; }

        .tst-dot[aria-selected="true"] {
            background: #1F6F67;
            width: 22px;
        }

        /* Arrows */
        .tst-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: 1px solid #E5E5E5;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #A3A3A3;
            cursor: pointer;
            transition: border-color 0.2s, color 0.2s;
            padding: 0;
            flex-shrink: 0;
            z-index: 2;
        }

        .dark .tst-arrow,
        [data-theme="dark"] .tst-arrow { border-color: #134643; }

        .tst-arrow:hover { border-color: #1F6F67; color: #1F6F67; }
        .tst-arrow-prev { left: 0; }
        .tst-arrow-next { right: 0; }

        /* Write a review button */
        .tst-review-wrap { display: flex; justify-content: center; }

        .tst-review-btn {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #1F6F67;
            background: none;
            border: 1px solid #1F6F67;
            padding: 9px 20px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }

        .dark .tst-review-btn,
        [data-theme="dark"] .tst-review-btn { color: #33A89F; border-color: #33A89F; }

        .tst-review-btn:hover { background: #1F6F67; color: #fff; }

        .dark .tst-review-btn:hover,
        [data-theme="dark"] .tst-review-btn:hover { background: #33A89F; color: #071E1E; }

        /* ── Modal ── */
        .tst-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(7, 30, 30, 0.55);
            backdrop-filter: blur(3px);
            z-index: 1001;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .tst-modal {
            background: #FCFCF9;
            width: 100%;
            max-width: 480px;
            padding: 36px;
            position: relative;
        }

        .dark .tst-modal,
        [data-theme="dark"] .tst-modal {
            background: #0D3230;
        }

        .tst-modal-close {
            position: absolute;
            top: 16px;
            right: 16px;
            background: none;
            border: none;
            cursor: pointer;
            color: #A3A3A3;
            padding: 4px;
            transition: color 0.2s;
            line-height: 1;
        }

        .tst-modal-close:hover { color: #111315; }

        .dark .tst-modal-close:hover,
        [data-theme="dark"] .tst-modal-close:hover { color: #F9F9F9; }

        .tst-modal-title {
            font-family: 'Manrope', sans-serif;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #111315;
            margin-bottom: 4px;
        }

        .dark .tst-modal-title,
        [data-theme="dark"] .tst-modal-title { color: #F9F9F9; }

        .tst-modal-sub {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 12px;
            color: #A3A3A3;
            margin-bottom: 28px;
        }

        .tst-field { margin-bottom: 16px; }

        .tst-label {
            display: block;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #525252;
            margin-bottom: 6px;
        }

        .dark .tst-label,
        [data-theme="dark"] .tst-label { color: #A3A3A3; }

        .tst-input,
        .tst-textarea {
            width: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px;
            color: #111315;
            background: #F3F3F3;
            border: 1px solid #E5E5E5;
            padding: 10px 14px;
            outline: none;
            transition: border-color 0.2s;
            border-radius: 0;
            -webkit-appearance: none;
        }

        .dark .tst-input,
        .dark .tst-textarea,
        [data-theme="dark"] .tst-input,
        [data-theme="dark"] .tst-textarea {
            background: #071E1E;
            border-color: #134643;
            color: #F9F9F9;
        }

        .tst-input:focus,
        .tst-textarea:focus { border-color: #1F6F67; }

        .tst-textarea { resize: vertical; min-height: 110px; }

        /* Star rating */
        .tst-stars {
            display: flex;
            gap: 4px;
            margin-bottom: 20px;
        }

        .tst-star {
            background: none;
            border: none;
            cursor: pointer;
            padding: 2px;
            color: #E5E5E5;
            transition: color 0.15s;
            font-size: 22px;
            line-height: 1;
        }

        .tst-star.active { color: #D9A21B; }

        .tst-modal-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 24px;
        }

        .tst-modal-note {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 10px;
            color: #A3A3A3;
            line-height: 1.5;
        }

        .tst-submit-btn {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #fff;
            background: #1F6F67;
            border: none;
            padding: 10px 22px;
            cursor: pointer;
            transition: background 0.2s;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .tst-submit-btn:hover { background: #195A55; }

        @media (max-width: 640px) {
            .tst-section { padding: 64px 24px; }
            .tst-stage { padding: 0 48px; min-height: 200px; }
            .tst-quote { width: calc(100% - 96px); }
            .tst-arrow { width: 30px; height: 30px; }
            .tst-modal { padding: 28px 20px; }
            .tst-modal-footer { flex-direction: column; align-items: flex-start; }
        }
    </style>

    <h2 id="tst-heading">Customer Testimonials — 1st Delightsome Fabrics</h2>

    <livewire:frontend.testimonials />

</section>