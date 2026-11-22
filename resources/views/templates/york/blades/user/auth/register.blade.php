@extends('templates.york.blades.layouts.auth')

@section('auth_showcase')
    <div class="relative min-h-[700px] overflow-hidden rounded-[2rem] border border-white/[0.09] bg-[#111318] shadow-[0_30px_90px_rgba(0,0,0,.6)]">
        <img src="{{ asset('assets/images/ai-trading-login-visual.png') }}"
            alt="{{ __('AI-powered crypto trading infrastructure') }}"
            class="absolute inset-0 h-full w-full object-cover object-center" fetchpriority="high">
        <div class="absolute inset-0 bg-gradient-to-b from-black/15 via-black/30 to-[#0b0e11]/95"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#0b0e11]/35 via-transparent to-transparent"></div>

        <div class="absolute left-7 top-7 flex items-center gap-2 rounded-full border border-white/10 bg-black/35 px-3 py-1.5 backdrop-blur-xl">
            <span class="h-1.5 w-1.5 rounded-full bg-amber-400 shadow-[0_0_10px_rgba(247,166,0,.9)]"></span>
            <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-white/80">{{ __('Build your trading workspace') }}</span>
        </div>

        <div class="absolute inset-x-0 bottom-0 p-7 xl:p-9">
            <div class="max-w-xl">
                <p class="mb-3 font-mono text-[10px] font-bold uppercase tracking-[0.24em] text-amber-400">
                    {{ __('One account. Smarter execution.') }}
                </p>
                <h2 class="font-heading text-3xl font-black leading-tight tracking-tight text-white xl:text-4xl">
                    {{ __('Start building your automated crypto portfolio.') }}
                </h2>
                <p class="mt-3 max-w-lg text-sm leading-6 text-slate-300">
                    {{ __('Create your secure account to access trading bots, portfolio analytics, and direct on-chain asset management.') }}
                </p>

                <div class="mt-6 space-y-2.5">
                    @foreach ([
                        __('Configure automated trading strategies'),
                        __('Monitor balances and performance in real time'),
                        __('Manage deposits and withdrawals on-chain'),
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
    @php
        $emailVerification = getSetting('email_verification');
        $fieldClass = 'h-11 w-full rounded-xl border border-white/[0.1] bg-[#0b0e11]/80 px-3.5 text-sm text-white outline-none transition placeholder:text-slate-700 hover:border-white/[0.16] focus:border-amber-400/70 focus:ring-4 focus:ring-amber-400/[0.08]';
        $labelClass = 'block text-[9px] font-bold uppercase tracking-[0.14em] text-slate-400';
    @endphp

    <div class="space-y-5" data-register-shell>
        @if (!session('register_info'))
            <div class="space-y-3">
                <div class="flex items-center justify-between gap-4">
                    <div class="inline-flex items-center gap-2 rounded-full border border-amber-400/15 bg-amber-400/[0.06] px-2.5 py-1 text-[9px] font-bold uppercase tracking-[0.18em] text-amber-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-400 shadow-[0_0_8px_rgba(247,166,0,.75)]"></span>
                        {{ __('New account') }}
                    </div>
                    <span class="font-mono text-[9px] uppercase tracking-[0.18em] text-slate-600">{{ __('User Portal') }}</span>
                </div>
                <div>
                    <h1 class="font-heading text-2xl font-black tracking-tight text-white sm:text-[28px]">{{ __('Create your account') }}</h1>
                    <p class="mt-1.5 text-xs leading-relaxed text-slate-400">{{ __('Set up secure access to your automated trading workspace.') }}</p>
                </div>
            </div>

            <div id="auth-selection" class="space-y-2.5" @if ($only_email) style="display: none;" @endif>
                @foreach ($login_methods as $method => $data)
                    @if ($method === 'email')
                        <button type="button" onclick="showEmailRegistration()"
                            class="group flex min-h-12 w-full items-center justify-between rounded-xl border border-amber-400/25 bg-amber-400/[0.07] px-4 py-3 text-left transition duration-200 hover:border-amber-400/45 hover:bg-amber-400/[0.11] focus:outline-none focus:ring-2 focus:ring-amber-400/30">
                            <span class="flex items-center gap-3">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-amber-400/20 bg-black/20 text-amber-300">{!! $data['icon'] !!}</span>
                                <span><span class="block text-xs font-bold text-white">{{ __('Register with email') }}</span><span class="mt-0.5 block text-[9px] text-slate-500">{{ __('Create credentials and verify your email') }}</span></span>
                            </span>
                            <svg class="h-4 w-4 text-amber-300 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @else
                        <a href="{{ route('user.login.social', ['provider' => $method]) }}"
                            class="group flex min-h-12 w-full items-center justify-between rounded-xl border px-4 py-3 transition duration-200 focus:outline-none focus:ring-2 focus:ring-amber-400/30 {{ $method === 'google' ? 'border-white bg-white text-slate-900 hover:bg-slate-100' : 'border-white/[0.09] bg-white/[0.035] text-white hover:border-white/[0.16] hover:bg-white/[0.065]' }}">
                            <span class="flex items-center gap-3">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg {{ $method === 'google' ? 'bg-slate-100' : 'border border-white/[0.08] bg-black/20' }}">{!! $data['icon'] !!}</span>
                                <span><span class="block text-xs font-bold">{{ __('Continue with :provider', ['provider' => $data['name']]) }}</span><span class="mt-0.5 block text-[9px] text-slate-500">{{ __('Secure provider registration') }}</span></span>
                            </span>
                            <svg class="h-4 w-4 opacity-50 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endif
                @endforeach
            </div>

            <div id="email-register-container" @if (!$only_email) style="display: none;" @endif class="space-y-4">
                @if (!$only_email)
                    <button type="button" onclick="showRegistrationOptions()" class="group inline-flex items-center gap-2 rounded-lg py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 transition hover:text-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-400/30">
                        <svg class="h-3.5 w-3.5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        {{ __('All registration methods') }}
                    </button>
                @endif

                <form action="{{ route('user.register-validate') }}" method="POST"
                    data-action="{{ $emailVerification == 'disabled' ? 'redirect' : 'reload' }}"
                    @if ($emailVerification == 'disabled') data-redirect="{{ route('user.dashboard') }}" @endif
                    class="ajax-form space-y-3.5">
                    @csrf

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="space-y-1.5"><label for="first_name" class="{{ $labelClass }}">{{ __('First name') }}</label><input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" class="{{ $fieldClass }}" placeholder="{{ __('John') }}" autocomplete="given-name" required></div>
                        <div class="space-y-1.5"><label for="last_name" class="{{ $labelClass }}">{{ __('Last name') }}</label><input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" class="{{ $fieldClass }}" placeholder="{{ __('Doe') }}" autocomplete="family-name" required></div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label for="username" class="{{ $labelClass }}">{{ __('Username') }}</label>
                            <div class="relative"><span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 font-mono text-xs text-slate-600">@</span><input type="text" name="username" id="username" value="{{ old('username') }}" class="{{ $fieldClass }} pl-8 font-mono" placeholder="johndoe" autocomplete="username" required></div>
                        </div>
                        <div class="space-y-1.5"><label for="email" class="{{ $labelClass }}">{{ __('Email address') }}</label><input type="email" name="email" id="email" value="{{ old('email') }}" class="{{ $fieldClass }}" placeholder="{{ __('john@example.com') }}" autocomplete="email" required></div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label for="password" class="{{ $labelClass }}">{{ __('Password') }}</label>
                            <div class="relative"><input type="password" name="password" id="password" class="{{ $fieldClass }} pr-10" placeholder="••••••••" autocomplete="new-password" required><button type="button" data-password-toggle="password" aria-label="{{ __('Show password') }}" class="password-toggle absolute inset-y-0 right-0 flex w-10 items-center justify-center text-slate-600 transition hover:text-amber-300"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7s-8.27-2.94-9.54-7z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg></button></div>
                        </div>
                        <div class="space-y-1.5">
                            <label for="password_confirmation" class="{{ $labelClass }}">{{ __('Confirm password') }}</label>
                            <div class="relative"><input type="password" name="password_confirmation" id="password_confirmation" class="{{ $fieldClass }} pr-10" placeholder="••••••••" autocomplete="new-password" required><button type="button" data-password-toggle="password_confirmation" aria-label="{{ __('Show password') }}" class="password-toggle absolute inset-y-0 right-0 flex w-10 items-center justify-center text-slate-600 transition hover:text-amber-300"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7s-8.27-2.94-9.54-7z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg></button></div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="referral_code" class="{{ $labelClass }}">{{ __('Referral code') }} <span class="normal-case tracking-normal text-slate-600">({{ __('optional') }})</span></label>
                        <div class="relative">
                            <input type="text" name="referral_code" id="referral_code"
                                @if (session()->has('referrer_code')) readonly value="{{ session('referrer_code') }}" @else value="{{ old('referral_code') }}" @endif
                                class="{{ $fieldClass }} font-mono uppercase @if(session()->has('referrer_code')) pr-20 font-bold text-emerald-400 @endif" placeholder="PROMO_CODE">
                            @if (session()->has('referrer_code'))<span class="absolute right-3 top-1/2 -translate-y-1/2 rounded-md border border-emerald-400/20 bg-emerald-400/10 px-2 py-0.5 text-[9px] font-bold uppercase text-emerald-400">{{ __('Applied') }}</span>@endif
                        </div>
                    </div>

                    @if (getSetting('google_recaptcha') == 'enabled')
                        <div class="flex flex-col items-center pt-1">{!! NoCaptcha::display(['data-theme' => 'dark']) !!}@error('g-recaptcha-response')<span class="mt-1 block text-xs text-red-400">{{ $message }}</span>@enderror</div>
                    @endif

                    <p class="text-[9px] leading-4 text-slate-600">{{ __('By creating an account, you confirm that the information provided is accurate and that you understand the risks associated with cryptocurrency trading.') }}</p>

                    <button type="submit" class="group flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-amber-400 via-[#f7a600] to-amber-500 px-4 py-3 text-xs font-black uppercase tracking-[0.12em] text-black shadow-[0_12px_28px_rgba(247,166,0,.2)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_16px_36px_rgba(247,166,0,.28)] focus:outline-none focus:ring-4 focus:ring-amber-400/25 disabled:cursor-not-allowed disabled:opacity-60">
                        <span>{{ __('Create account') }}</span><svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5-5 5M6 12h12"/></svg>
                    </button>
                </form>
            </div>
        @else
            <div class="relative space-y-6 pt-1">
                <form action="{{ route('user.register-cancel') }}" method="POST" class="ajax-form absolute right-0 top-0 z-20" data-action="redirect" data-redirect="{{ route('user.register') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-white/[0.08] bg-white/[0.03] px-2.5 py-1.5 text-[9px] font-bold uppercase tracking-wider text-slate-500 transition hover:bg-white/[0.07] hover:text-white"><svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>{{ __('Start over') }}</button>
                </form>

                <div class="pt-8 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl border border-amber-400/20 bg-amber-400/[0.08] text-amber-400 shadow-[0_0_24px_rgba(247,166,0,.08)]"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-16 11h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                    <p class="mb-2 font-mono text-[9px] font-bold uppercase tracking-[0.2em] text-amber-400">{{ __('Email verification') }}</p>
                    <h1 class="font-heading text-2xl font-black tracking-tight text-white">{{ __('Check your inbox') }}</h1>
                    <p class="mt-2 text-xs leading-5 text-slate-400">{{ __('Enter the 6-digit verification code sent to') }}<br><span class="font-mono text-sm font-bold text-white">{{ session('register_info.email') }}</span></p>
                </div>

                <form action="{{ route('user.email-verification') }}" method="POST" data-action="redirect" data-redirect="{{ route('user.dashboard') }}" class="ajax-form space-y-4">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('register_info.email') }}">
                    <div><label for="otp_code" class="sr-only">{{ __('Verification code') }}</label><input type="text" inputmode="numeric" pattern="[0-9]*" name="otp_code" id="otp_code" class="h-14 w-full rounded-xl border border-white/[0.12] bg-[#0b0e11]/90 px-4 text-center font-mono text-2xl font-bold tracking-[0.55em] text-white outline-none transition placeholder:text-slate-700 focus:border-amber-400/70 focus:ring-4 focus:ring-amber-400/[0.08]" placeholder="••••••" required maxlength="6" autocomplete="one-time-code" autofocus></div>
                    <button type="submit" class="flex min-h-12 w-full items-center justify-center rounded-xl bg-gradient-to-r from-amber-400 via-[#f7a600] to-amber-500 px-4 py-3 text-xs font-black uppercase tracking-[0.12em] text-black shadow-[0_12px_28px_rgba(247,166,0,.2)] transition hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-amber-400/25">{{ __('Confirm and activate account') }}</button>
                </form>

                <div class="text-center">
                    <p class="text-[10px] text-slate-500">{{ __("Didn't receive the code?") }}</p>
                    <form action="{{ route('user.resend-verification') }}" method="POST" class="ajax-form mt-1 inline-block">@csrf<button type="submit" id="resend-btn" class="border-0 bg-transparent p-0 text-[10px] font-bold text-amber-400 transition hover:text-amber-300 disabled:cursor-not-allowed disabled:opacity-40">{{ __('Resend verification code') }}</button></form>
                    <div id="countdown-timer" class="mt-1 hidden font-mono text-[10px] text-slate-600">{{ __('Available again in') }} <span id="timer-seconds" class="font-bold text-amber-400">60</span>s</div>
                </div>
            </div>
        @endif

        <div class="border-t border-white/[0.06] pt-4 text-center">
            <p class="text-[11px] text-slate-500">{{ __('Already have an account?') }} <a href="{{ route('user.login') }}" class="ml-1 font-bold text-amber-400 transition hover:text-amber-300 hover:underline">{{ __('Sign in') }}</a></p>
        </div>
    </div>
@endsection

@section('scripts')
    @if (getSetting('google_recaptcha') == 'enabled')
        {!! NoCaptcha::renderJs() !!}
    @endif

    <script>
        $(document).ready(function() {
            const resendBtn = $('#resend-btn');
            const countdownTimer = $('#countdown-timer');
            const timerSeconds = $('#timer-seconds');
            let timeLeft = 60;
            let timerInterval;
            let storedThrottle = localStorage.getItem('resend_throttle_expiry');
            const backendThrottle = @json($throttle ?? false);

            if (backendThrottle && (!storedThrottle || storedThrottle < Date.now())) {
                storedThrottle = Date.now() + 60000;
                localStorage.setItem('resend_throttle_expiry', storedThrottle);
            }

            function startTimer() {
                resendBtn.prop('disabled', true);
                countdownTimer.removeClass('hidden');
                if (timerInterval) clearInterval(timerInterval);
                timerInterval = setInterval(function() {
                    timeLeft--;
                    timerSeconds.text(timeLeft);
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        resendBtn.prop('disabled', false);
                        countdownTimer.addClass('hidden');
                        timeLeft = 60;
                        localStorage.removeItem('resend_throttle_expiry');
                    }
                }, 1000);
            }

            if (storedThrottle) {
                const distance = storedThrottle - Date.now();
                if (distance > 0) { timeLeft = Math.max(1, Math.floor(distance / 1000)); startTimer(); }
                else localStorage.removeItem('resend_throttle_expiry');
            }

            $('form[action="{{ route('user.resend-verification') }}"]').on('submit', function() {
                const expiry = Date.now() + 60000;
                localStorage.setItem('resend_throttle_expiry', expiry);
                timeLeft = 60;
                startTimer();
            });

            document.querySelectorAll('[data-password-toggle]').forEach((button) => {
                button.addEventListener('click', () => {
                    const input = document.getElementById(button.dataset.passwordToggle);
                    const show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    button.setAttribute('aria-label', show ? @json(__('Hide password')) : @json(__('Show password')));
                    input.focus();
                });
            });
        });

        function showEmailRegistration() {
            document.getElementById('auth-selection')?.style.setProperty('display', 'none');
            document.getElementById('email-register-container')?.style.setProperty('display', 'block');
            window.setTimeout(() => document.getElementById('first_name')?.focus(), 50);
        }

        function showRegistrationOptions() {
            document.getElementById('auth-selection')?.style.setProperty('display', 'block');
            document.getElementById('email-register-container')?.style.setProperty('display', 'none');
        }
    </script>
@endsection
