{{-- resources/views/frontend/checkout/index.blade.php --}}
@extends('layouts.custom', ['alwaysShowHeaderBg' => true])

@section('content')

{{-- Dark mode store — must be initialised before Alpine processes $store.theme.dark in the layout --}}
<script>
    // Synchronous: prevent FOUC by applying class before paint
    (function () {
        var saved = localStorage.getItem('theme');
        if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();

    document.addEventListener('alpine:init', () => {
        Alpine.store('theme', {
            dark: localStorage.getItem('theme') === 'dark'
                || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
            toggle() {
                this.dark = !this.dark;
                document.documentElement.classList.toggle('dark', this.dark);
                localStorage.setItem('theme', this.dark ? 'dark' : 'light');
            }
        });
    });
</script>

<livewire:frontend.checkout />
@endsection
