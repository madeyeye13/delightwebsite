<x-guest-layout image="{{ asset('images/auth/login-bg.jpg') }}">

    <div class="anim-heading mb-3">
        <h1 class="font-display text-3xl font-semibold leading-tight tracking-tight text-white">
            Confirm Your<br>
            <em class="font-normal not-italic text-neutral-400">Password</em>
        </h1>
    </div>

    <p class="anim-sub text-sm font-light text-neutral-400 leading-relaxed mb-9">
        This is a secure area. Please confirm your password before continuing.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}"
          x-data="{ loading: false }"
          @submit="loading = true">
        @csrf

        {{-- Password --}}
        <div class="anim-field mb-7">
            <label for="password"
                   class="block text-2xs font-medium tracking-widest uppercase text-neutral-400 mb-2">
                Password *
            </label>
            <div class="input-wrap flex items-center">
                <input
                    id="password" type="password" name="password"
                    placeholder="••••••••••"
                    required autofocus autocomplete="current-password"
                    class="w-full bg-transparent border-0 border-b border-white/20
                           text-white text-base font-light placeholder-neutral-600
                           pb-2 px-0 pr-12 focus:border-white/20 transition-colors duration-300"
                />
                <button type="button" data-pw-toggle
                        class="absolute right-0 bottom-2 text-2xs font-medium tracking-widest
                               uppercase text-neutral-500 hover:text-white transition-colors
                               duration-200 bg-transparent border-0 cursor-pointer">
                    Show
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="anim-actions pt-2">
            <button type="submit"
                    :disabled="loading"
                    :class="loading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-white hover:text-ink-soft hover:border-white cursor-pointer'"
                    class="inline-flex items-center gap-2.5
                           border border-white/60 bg-transparent
                           text-white text-xs font-medium tracking-widest uppercase
                           px-9 py-3.5 transition-all duration-300 select-none">

                <svg x-show="loading" x-cloak
                     class="w-3.5 h-3.5 animate-spin"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="3"/>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                </svg>

                <span x-text="loading ? 'Confirming…' : 'Confirm'">Confirm</span>
            </button>
        </div>

    </form>

</x-guest-layout>