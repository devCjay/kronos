@extends('templates.york.blades.layouts.auth')

@section('auth_showcase')
    <div class="relative min-h-[700px] overflow-hidden rounded-[2rem] border border-white/[0.09] bg-[#111318] shadow-[0_30px_90px_rgba(0,0,0,.6)]">
        <img src="{{ asset('assets/images/ai-trading-login-visual.png') }}"
            alt="{{ __('Secure crypto account recovery verification') }}"
            class="absolute inset-0 h-full w-full object-cover object-center" fetchpriority="high">
        <div class="absolute inset-0 bg-gradient-to-b from-black/15 via-black/35 to-[#0b0e11]/95"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#0b0e11]/35 via-transparent to-transparent"></div>

        <div class="absolute left-7 top-7 flex items-center gap-2 rounded-full border border-white/10 bg-black/35 px-3 py-1.5 backdrop-blur-xl">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 shadow-[0_0_10px_rgba(52,211,153,.9)]"></span>
            <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-white/80">{{ __('Identity verification') }}</span>
        </div>

        <div class="absolute inset-x-0 bottom-0 p-7 xl:p-9">
            <div class="max-w-xl">
                <p class="mb-3 font-mono text-[10px] font-bold uppercase tracking-[0.24em] text-amber-400">
                    {{ __('Confirm account ownership') }}
                </p>
                <h2 class="font-heading text-3xl font-black leading-tight tracking-tight text-white xl:text-4xl">
                    {{ __('A secure checkpoint before your password changes.') }}
                </h2>
                <p class="mt-3 max-w-lg text-sm leading-6 text-slate-300">
                    {{ __('The recovery code confirms that you control the email address connected to your trading account.') }}
                </p>

                <div class="mt-6 grid gap-2.5 sm:grid-cols-3">
                    @foreach ([
                        [__('6-digit code'), __('Single-use verification')],
                        [__('15 minutes'), __('Limited code lifetime')],
                        [__('Session bound'), __('Protected recovery flow')],
                    ] as [$title, $description])
                        <div class="rounded-xl border border-white/[0.07] bg-black/25 px-3.5 py-3 backdrop-blur-xl">
                            <div class="text-[11px] font-bold text-white">{{ $title }}</div>
                            <div class="mt-1 text-[9px] leading-4 text-slate-400">{{ $description }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @php
        $recoveryEmail = session('user_password_reset_email');
    @endphp

    <div class="space-y-5">
        <div class="space-y-3">
            <div class="flex items-center justify-between gap-4">
                <div class="inline-flex items-center gap-2 rounded-full border border-emerald-400/15 bg-emerald-400/[0.06] px-2.5 py-1 text-[9px] font-bold uppercase tracking-[0.18em] text-emerald-400">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,.75)]"></span>
                    {{ __('Recovery verification') }}
                </div>
                <span class="font-mono text-[9px] uppercase tracking-[0.18em] text-slate-600">{{ __('Step 2 of 3') }}</span>
            </div>

            <div>
                <h1 class="font-heading text-2xl font-black tracking-tight text-white sm:text-[28px]">{{ __('Check your email') }}</h1>
                <p class="mt-1.5 text-xs leading-relaxed text-slate-400">
                    {{ __('Enter the 6-digit recovery code we sent to your registered email address.') }}
                </p>
            </div>
        </div>

        @if ($recoveryEmail)
            <div class="flex items-center gap-3 rounded-xl border border-white/[0.07] bg-white/[0.025] px-3.5 py-3">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-amber-400/15 bg-amber-400/[0.07] text-amber-300">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="text-[9px] font-bold uppercase tracking-[0.14em] text-slate-500">{{ __('Recovery code sent to') }}</div>
                    <div class="mt-0.5 truncate text-xs font-semibold text-slate-200">{{ $recoveryEmail }}</div>
                </div>
            </div>
        @endif

        <form action="{{ route('user.forgot-password.otp.validate') }}" method="POST" class="ajax-form space-y-4" data-action="redirect">
            @csrf

            <div class="space-y-2">
                <div class="flex items-center justify-between gap-3">
                    <label for="otp_code" class="text-[9px] font-bold uppercase tracking-[0.14em] text-slate-400">{{ __('Recovery code') }}</label>
                    <span class="font-mono text-[9px] text-slate-600">{{ __('6 digits') }}</span>
                </div>
                <input type="text" name="otp_code" id="otp_code"
                    class="h-16 w-full rounded-xl border border-white/[0.1] bg-[#0b0e11]/80 px-4 text-center font-mono text-2xl font-bold tracking-[0.55em] text-white outline-none transition placeholder:text-slate-700 hover:border-white/[0.16] focus:border-amber-400/70 focus:ring-4 focus:ring-amber-400/[0.08]"
                    placeholder="••••••" maxlength="6" inputmode="numeric" pattern="[0-9]*" required autofocus autocomplete="one-time-code"
                    aria-describedby="recovery-code-help"
                    @if (session()->has('user_password_reset_otp_code') && config('app.env') === 'sandbox') value="{{ session('user_password_reset_otp_code') }}" @endif>
                <p id="recovery-code-help" class="text-[10px] leading-4 text-slate-500">{{ __('For your security, this code expires after 15 minutes and can only be used for this recovery session.') }}</p>
                @error('otp_code')
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
                    <span>{{ __('Verify & Continue') }}</span>
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </span>
            </button>
        </form>

        <div class="rounded-xl border border-white/[0.07] bg-white/[0.02] px-4 py-3 text-center">
            <p class="text-[10px] leading-4 text-slate-500">{{ __('Did not receive the code or entered the wrong email?') }}</p>
            <a href="{{ route('user.forgot-password') }}" class="group mt-1.5 inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.1em] text-amber-400 transition hover:text-amber-300">
                <svg class="h-3.5 w-3.5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>{{ __('Restart recovery') }}</span>
            </a>
        </div>
    </div>
@endsection

@section('scripts')
    @if (getSetting('google_recaptcha') == 'enabled')
        {!! NoCaptcha::renderJs() !!}
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const recoveryCode = document.getElementById('otp_code');

            recoveryCode?.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 6);
            });
        });
    </script>
@endsection
