<x-guest-layout image="{{ asset('images/auth/20.jpg') }}">

    {{-- Session Status --}}
    @if (session('status'))
        <div class="mb-6 px-4 py-3
                    bg-accent-900/30 border border-accent-700/40
                    text-accent-300 text-sm font-normal tracking-wide">
            {{ session('status') }}
        </div>
    @endif

    {{-- Heading --}}
    <div class="anim-heading mb-8">
        <h1 class="font-display text-3xl font-semibold leading-tight tracking-tight text-white">
            Sign in to Access<br>
            <em class="font-normal not-italic text-neutral-400">Your Account</em>
        </h1>
    </div>

    {{-- x-data on the form: loading starts false, flips true on submit --}}
    <form method="POST" action="{{ route('login') }}"
          x-data="{ loading: false }"
          @submit.prevent="if (!loading) { loading = true; $nextTick(() => $el.submit()) }">
        @csrf

        {{-- Email --}}
        <div class="anim-field mb-7">
            <label for="email"
                   class="block text-2xs font-medium tracking-widest uppercase text-neutral-400 mb-2">
                Email *
            </label>
            <div class="input-wrap">
                <input
                    id="email" type="email" name="email"
                    value="{{ old('email') }}" placeholder="you@example.com"
                    required autofocus autocomplete="username"
                    class="w-full bg-transparent border-0 border-b border-white/20
                           text-white text-base font-light placeholder-neutral-600
                           pb-2 px-0 focus:border-white/20 transition-colors duration-300"
                />
            </div>
            @error('email')
                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

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
                    required autocomplete="current-password"
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

        {{-- Remember Me --}}
        <div class="anim-field mb-0">
            <label class="inline-flex items-center gap-2.5 cursor-pointer group">
                <input type="checkbox" name="remember"
                       class="w-3.5 h-3.5 rounded-none border border-white/20 bg-transparent
                              accent-accent-500 cursor-pointer" />
                <span class="text-xs font-normal text-neutral-400 group-hover:text-neutral-300
                             transition-colors duration-200">Remember me</span>
            </label>
        </div>

        {{-- Actions --}}
        <div class="anim-actions flex items-center gap-5 flex-wrap pt-8">

            {{-- Submit button: fades to 50% and disables while loading --}}
            <button type="submit"
                    :disabled="loading"
                    :class="loading ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:bg-white hover:text-ink-soft hover:border-white cursor-pointer'"
                    class="inline-flex items-center gap-2.5
                           border border-white/60 bg-transparent
                           text-white text-xs font-medium tracking-widest uppercase
                           px-9 py-3.5 transition-all duration-300 select-none">

                {{-- Spinner (visible when loading) --}}
                <svg x-show="loading" x-cloak
                     class="w-3.5 h-3.5 animate-spin"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="3"/>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                </svg>

                {{-- Label swaps on loading --}}
                <span x-text="loading ? 'Signing In…' : 'Sign In'">Sign In</span>
            </button>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm font-normal text-neutral-400 hover:text-white
                          underline underline-offset-4 decoration-white/20 hover:decoration-white
                          transition-all duration-200">
                    Forgot your password?
                </a>
            @endif
        </div>

    </form>

    {{-- Divider + Register --}}
    <div class="anim-footer mt-10">
        <hr class="border-t border-white/10 mb-6">
        <p class="text-xs font-normal text-neutral-500 mb-3 tracking-wide">
            Don't Have an Account?
        </p>
        <a href="{{ route('register') }}"
           class="inline-block border border-white/20 bg-transparent
                  text-neutral-400 hover:text-white hover:border-white/50
                  text-xs font-medium tracking-widest uppercase
                  px-8 py-3 transition-all duration-250">
            Create Account
        </a>
    </div>

</x-guest-layout>