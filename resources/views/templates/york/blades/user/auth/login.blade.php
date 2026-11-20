@extends('templates.york.blades.layouts.auth')

@section('content')
    <div class="space-y-6" data-login-shell>
        {{-- Page identity --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between gap-4">
                <div class="inline-flex items-center gap-2 rounded-full border border-emerald-400/15 bg-emerald-400/[0.06] px-2.5 py-1 text-[9px] font-bold uppercase tracking-[0.18em] text-emerald-400">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,.75)]"></span>
                    {{ __('Secure account access') }}
                </div>
                <span class="font-mono text-[9px] uppercase tracking-[0.18em] text-slate-600">{{ __('User Portal') }}</span>
            </div>

            <div>
                <h1 class="font-heading text-2xl font-black tracking-tight text-white sm:text-[28px]">
                    {{ __('Welcome back') }}
                </h1>
                <p class="mt-1.5 text-xs leading-relaxed text-slate-400">
                    {{ __('Sign in securely to manage your portfolio and automated trading bots.') }}
                </p>
            </div>
        </div>

        {{-- Provider selection --}}
        <div id="auth-selection" class="space-y-3" @if ($only_email) style="display: none;" @endif>
            <div class="space-y-2.5">
                @foreach ($login_methods as $method => $data)
                    @if ($method === 'email')
                        <button type="button" onclick="showEmailLogin()"
                            class="group flex min-h-12 w-full items-center justify-between rounded-xl border border-amber-400/25 bg-amber-400/[0.07] px-4 py-3 text-left transition duration-200 hover:border-amber-400/45 hover:bg-amber-400/[0.11] focus:outline-none focus:ring-2 focus:ring-amber-400/30">
                            <span class="flex items-center gap-3">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-amber-400/20 bg-black/20 text-amber-300 transition group-hover:border-amber-400/35">
                                    {!! $data['icon'] !!}
                                </span>
                                <span>
                                    <span class="block text-xs font-bold text-white">{{ __('Email and password') }}</span>
                                    <span class="mt-0.5 block text-[9px] text-slate-500">{{ __('Use your registered account credentials') }}</span>
                                </span>
                            </span>
                            <svg class="h-4 w-4 text-amber-300 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @else
                        <a href="{{ route('user.login.social', ['provider' => $method]) }}"
                            class="group flex min-h-12 w-full items-center justify-between rounded-xl border px-4 py-3 transition duration-200 focus:outline-none focus:ring-2 focus:ring-amber-400/30 {{ $method === 'google' ? 'border-white bg-white text-slate-900 hover:bg-slate-100' : 'border-white/[0.09] bg-white/[0.035] text-white hover:border-white/[0.16] hover:bg-white/[0.065]' }}">
                            <span class="flex items-center gap-3">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg {{ $method === 'google' ? 'bg-slate-100' : 'border border-white/[0.08] bg-black/20' }}">
                                    {!! $data['icon'] !!}
                                </span>
                                <span>
                                    <span class="block text-xs font-bold">{{ __('Continue with :provider', ['provider' => $data['name']]) }}</span>
                                    <span class="mt-0.5 block text-[9px] {{ $method === 'google' ? 'text-slate-500' : 'text-slate-500' }}">{{ __('Secure provider authentication') }}</span>
                                </span>
                            </span>
                            <svg class="h-4 w-4 opacity-50 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endif
                @endforeach
            </div>

            <p class="px-3 text-center text-[9px] leading-relaxed text-slate-600">
                {{ __('Choose an approved sign-in method associated with your account.') }}
            </p>
        </div>

        {{-- Email login --}}
        <div id="email-login-container" @if (!$only_email) style="display: none;" @endif class="space-y-5">
            @if (!$only_email)
                <button type="button" onclick="showLoginOptions()"
                    class="group inline-flex items-center gap-2 rounded-lg py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 transition hover:text-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-400/30">
                    <svg class="h-3.5 w-3.5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ __('All sign-in methods') }}
                </button>
            @endif

            <form action="{{ route('user.login.validate') }}" method="POST" class="ajax-form space-y-4" data-action="redirect">
                @csrf

                <div class="space-y-1.5">
                    <label for="email" class="block text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">
                        {{ __('Email address') }}
                    </label>
                    <div class="group relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-600 transition group-focus-within:text-amber-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-16 11h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            class="h-12 w-full rounded-xl border border-white/[0.1] bg-[#0b0e11]/80 py-3 pl-10 pr-4 text-sm text-white outline-none transition placeholder:text-slate-700 hover:border-white/[0.16] focus:border-amber-400/70 focus:ring-4 focus:ring-amber-400/[0.08]"
                            placeholder="{{ __('name@example.com') }}">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-4">
                        <label for="password" class="block text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">
                            {{ __('Password') }}
                        </label>
                        <a href="{{ route('user.forgot-password') }}" class="text-[10px] font-bold text-amber-400 transition hover:text-amber-300 hover:underline">
                            {{ __('Forgot password?') }}
                        </a>
                    </div>
                    <div class="group relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-600 transition group-focus-within:text-amber-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect width="16" height="11" x="4" y="10" rx="2" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M8 10V7a4 4 0 118 0v3"/></svg>
                        </span>
                        <input type="password" name="password" id="password" required autocomplete="current-password"
                            class="h-12 w-full rounded-xl border border-white/[0.1] bg-[#0b0e11]/80 py-3 pl-10 pr-12 text-sm text-white outline-none transition placeholder:text-slate-700 hover:border-white/[0.16] focus:border-amber-400/70 focus:ring-4 focus:ring-amber-400/[0.08]"
                            placeholder="••••••••">
                        <button type="button" id="password-visibility" aria-label="{{ __('Show password') }}" aria-pressed="false"
                            class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-600 transition hover:text-amber-300 focus:outline-none focus:text-amber-300">
                            <svg data-password-icon="show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7s-8.27-2.94-9.54-7z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>
                            <svg data-password-icon="hide" class="hidden h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 4.24A10.8 10.8 0 0112 4c4.48 0 8.27 2.94 9.54 7a11.7 11.7 0 01-2.15 3.78M6.61 6.61A11.5 11.5 0 002.46 11c1.27 4.06 5.06 7 9.54 7 1.12 0 2.2-.18 3.2-.5"/></svg>
                        </button>
                    </div>
                    <p id="caps-lock-warning" class="hidden text-[9px] font-medium text-amber-400" role="status">
                        {{ __('Caps Lock is on') }}
                    </p>
                </div>

                <div class="flex items-center justify-between gap-4 pt-0.5">
                    <label class="flex cursor-pointer select-none items-center gap-2.5 text-[11px] text-slate-400 transition hover:text-slate-300">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 rounded border-white/20 bg-[#0b0e11] text-amber-500 focus:ring-2 focus:ring-amber-500/30 focus:ring-offset-0">
                        <span>{{ __('Remember this browser') }}</span>
                    </label>
                    <span class="inline-flex items-center gap-1.5 text-[9px] text-slate-600">
                        <svg class="h-3 w-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('Encrypted session') }}
                    </span>
                </div>

                @if (getSetting('google_recaptcha') == 'enabled')
                    <div class="flex flex-col items-center pt-1">
                        {!! NoCaptcha::display(['data-theme' => 'dark']) !!}
                        @error('g-recaptcha-response')
                            <span class="mt-1 block text-xs text-red-400">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <button type="submit"
                    class="group relative flex min-h-12 w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-gradient-to-r from-amber-400 via-[#f7a600] to-amber-500 px-4 py-3 text-xs font-black uppercase tracking-[0.12em] text-black shadow-[0_12px_28px_rgba(247,166,0,.2)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_16px_36px_rgba(247,166,0,.28)] focus:outline-none focus:ring-4 focus:ring-amber-400/25 disabled:cursor-not-allowed disabled:opacity-60">
                    <span>{{ __('Sign in securely') }}</span>
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5-5 5M6 12h12"/></svg>
                </button>
            </form>
        </div>

        <div class="border-t border-white/[0.06] pt-5 text-center">
            <p class="text-[11px] text-slate-500">
                {{ __("Don't have an account?") }}
                <a href="{{ route('user.register') }}" class="ml-1 font-bold text-amber-400 transition hover:text-amber-300 hover:underline">
                    {{ __('Create an account') }}
                </a>
            </p>
        </div>
    </div>
@endsection

@section('scripts')
    @if (getSetting('google_recaptcha') == 'enabled')
        {!! NoCaptcha::renderJs() !!}
    @endif

    <script>
        function showEmailLogin() {
            const selection = document.getElementById('auth-selection');
            const emailLogin = document.getElementById('email-login-container');
            if (selection) selection.style.display = 'none';
            if (emailLogin) emailLogin.style.display = 'block';
            window.setTimeout(() => document.getElementById('email')?.focus(), 50);
        }

        function showLoginOptions() {
            const selection = document.getElementById('auth-selection');
            const emailLogin = document.getElementById('email-login-container');
            if (selection) selection.style.display = 'block';
            if (emailLogin) emailLogin.style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const password = document.getElementById('password');
            const visibility = document.getElementById('password-visibility');
            const capsWarning = document.getElementById('caps-lock-warning');

            visibility?.addEventListener('click', () => {
                const isVisible = password.type === 'text';
                password.type = isVisible ? 'password' : 'text';
                visibility.setAttribute('aria-pressed', String(!isVisible));
                visibility.setAttribute('aria-label', isVisible ? @json(__('Show password')) : @json(__('Hide password')));
                visibility.querySelector('[data-password-icon="show"]')?.classList.toggle('hidden', !isVisible);
                visibility.querySelector('[data-password-icon="hide"]')?.classList.toggle('hidden', isVisible);
                password.focus();
            });

            const updateCapsLock = (event) => {
                capsWarning?.classList.toggle('hidden', !event.getModifierState('CapsLock'));
            };
            password?.addEventListener('keydown', updateCapsLock);
            password?.addEventListener('keyup', updateCapsLock);
            password?.addEventListener('blur', () => capsWarning?.classList.add('hidden'));
        });
    </script>
@endsection
