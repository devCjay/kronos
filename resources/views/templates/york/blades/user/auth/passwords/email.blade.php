@extends('templates.york.blades.layouts.auth')

@section('auth_showcase')
    <div class="relative min-h-[700px] overflow-hidden rounded-[2rem] border border-white/[0.09] bg-[#111318] shadow-[0_30px_90px_rgba(0,0,0,.6)]">
        <img src="{{ asset('assets/images/ai-trading-login-visual.png') }}"
            alt="{{ __('Secure crypto account recovery') }}"
            class="absolute inset-0 h-full w-full object-cover object-center" fetchpriority="high">
        <div class="absolute inset-0 bg-gradient-to-b from-black/15 via-black/35 to-[#0b0e11]/95"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#0b0e11]/35 via-transparent to-transparent"></div>

        <div class="absolute left-7 top-7 flex items-center gap-2 rounded-full border border-white/10 bg-black/35 px-3 py-1.5 backdrop-blur-xl">
            <span class="h-1.5 w-1.5 rounded-full bg-amber-400 shadow-[0_0_10px_rgba(247,166,0,.9)]"></span>
            <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-white/80">{{ __('Secure account recovery') }}</span>
        </div>

        <div class="absolute inset-x-0 bottom-0 p-7 xl:p-9">
            <div class="max-w-xl">
                <p class="mb-3 font-mono text-[10px] font-bold uppercase tracking-[0.24em] text-amber-400">
                    {{ __('Regain access safely') }}
                </p>
                <h2 class="font-heading text-3xl font-black leading-tight tracking-tight text-white xl:text-4xl">
                    {{ __('Your trading workspace remains protected.') }}
                </h2>
                <p class="mt-3 max-w-lg text-sm leading-6 text-slate-300">
                    {{ __('We verify ownership through your registered email before allowing any password change.') }}
                </p>

                <div class="mt-6 space-y-2.5">
                    @foreach ([
                        __('A single-use recovery code is sent to your email'),
                        __('The verification code expires after 15 minutes'),
                        __('Your portfolio and trading settings remain unchanged'),
                    ] as $benefit)
                        <div class="flex items-center gap-3 rounded-xl border border-white/[0.07] bg-black/25 px-3.5 py-2.5 backdrop-blur-xl">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-400/10 text-emerald-400">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span class="text-[11px] font-semibold text-slate-200">{{ $benefit }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-5">
        <div class="space-y-3">
            <div class="flex items-center justify-between gap-4">
                <div class="inline-flex items-center gap-2 rounded-full border border-amber-400/15 bg-amber-400/[0.06] px-2.5 py-1 text-[9px] font-bold uppercase tracking-[0.18em] text-amber-400">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400 shadow-[0_0_8px_rgba(247,166,0,.75)]"></span>
                    {{ __('Account recovery') }}
                </div>
                <span class="font-mono text-[9px] uppercase tracking-[0.18em] text-slate-600">{{ __('Step 1 of 3') }}</span>
            </div>

            <div>
                <h1 class="font-heading text-2xl font-black tracking-tight text-white sm:text-[28px]">{{ __('Forgot your password?') }}</h1>
                <p class="mt-1.5 text-xs leading-relaxed text-slate-400">
                    {{ __('Enter the email linked to your account and we will send you a 6-digit recovery code.') }}
                </p>
            </div>
        </div>

        <div class="flex items-start gap-3 rounded-xl border border-sky-400/10 bg-sky-400/[0.04] px-3.5 py-3">
            <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-sky-400/[0.08] text-sky-300">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 11c0-1.1.9-2 2-2s2 .9 2 2-.9 2-2 2v1m0 3h.01M12 3a9 9 0 100 18 9 9 0 000-18z"/></svg>
            </span>
            <p class="text-[10px] leading-5 text-slate-400">{{ __('Use the same email address you use to sign in. For security, recovery instructions are only delivered to registered accounts.') }}</p>
        </div>

        <form action="{{ route('user.forgot-password.send') }}" method="POST" class="ajax-form space-y-4" data-action="redirect">
            @csrf

            <div class="space-y-1.5">
                <label for="email" class="block text-[9px] font-bold uppercase tracking-[0.14em] text-slate-400">{{ __('Email address') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="h-11 w-full rounded-xl border border-white/[0.1] bg-[#0b0e11]/80 pl-10 pr-3.5 text-sm text-white outline-none transition placeholder:text-slate-700 hover:border-white/[0.16] focus:border-amber-400/70 focus:ring-4 focus:ring-amber-400/[0.08]"
                        placeholder="{{ __('john@example.com') }}" autocomplete="email" required autofocus>
                </div>
                @error('email')
                    <p class="text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            @if (getSetting('google_recaptcha') == 'enabled')
                <div class="flex flex-col items-center overflow-hidden rounded-xl border border-white/[0.06] bg-black/10 py-3">
                    {!! NoCaptcha::display(['data-theme' => 'dark']) !!}
                    @error('g-recaptcha-response')
                        <span class="mt-2 block text-xs text-rose-400">{{ $message }}</span>
                    @enderror
                </div>
            @endif

            <button type="submit"
                class="group relative w-full cursor-pointer overflow-hidden rounded-xl bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 px-4 py-3.5 text-sm font-extrabold text-black shadow-[0_0_30px_rgba(247,166,0,0.2)] transition duration-300 hover:-translate-y-0.5 hover:from-amber-300 hover:to-amber-500 focus:outline-none focus:ring-4 focus:ring-amber-400/20">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <span>{{ __('Send recovery code') }}</span>
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </span>
            </button>
        </form>

        <div class="border-t border-white/[0.06] pt-4 text-center">
            <a href="{{ route('user.login') }}" class="group inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.1em] text-slate-500 transition hover:text-white">
                <svg class="h-3.5 w-3.5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>{{ __('Back to login') }}</span>
            </a>
        </div>
    </div>
@endsection

@section('scripts')
    @if (getSetting('google_recaptcha') == 'enabled')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endsection
