@extends('templates.york.blades.layouts.auth')

@section('auth_showcase')
    <div class="relative min-h-[700px] overflow-hidden rounded-[2rem] border border-white/[0.09] bg-[#111318] shadow-[0_30px_90px_rgba(0,0,0,.6)]">
        <img src="{{ asset('assets/images/ai-trading-login-visual.png') }}"
            alt="{{ __('Secure your AI trading account') }}"
            class="absolute inset-0 h-full w-full object-cover object-center" fetchpriority="high">
        <div class="absolute inset-0 bg-gradient-to-b from-black/15 via-black/35 to-[#0b0e11]/95"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#0b0e11]/35 via-transparent to-transparent"></div>

        <div class="absolute left-7 top-7 flex items-center gap-2 rounded-full border border-white/10 bg-black/35 px-3 py-1.5 backdrop-blur-xl">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 shadow-[0_0_10px_rgba(52,211,153,.9)]"></span>
            <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-white/80">{{ __('Verified recovery session') }}</span>
        </div>

        <div class="absolute inset-x-0 bottom-0 p-7 xl:p-9">
            <div class="max-w-xl">
                <p class="mb-3 font-mono text-[10px] font-bold uppercase tracking-[0.24em] text-amber-400">
                    {{ __('Complete account recovery') }}
                </p>
                <h2 class="font-heading text-3xl font-black leading-tight tracking-tight text-white xl:text-4xl">
                    {{ __('Secure your next trading session.') }}
                </h2>
                <p class="mt-3 max-w-lg text-sm leading-6 text-slate-300">
                    {{ __('Set a new password to restore access while keeping your portfolio, strategies, and account preferences intact.') }}
                </p>

                <div class="mt-6 space-y-2.5">
                    @foreach ([
                        __('Your email ownership has been verified'),
                        __('The new password replaces your previous credentials'),
                        __('Your portfolio and trading configuration stay unchanged'),
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
        $recoveryEmail = session('user_password_reset_email');
        $fieldClass = 'h-11 w-full rounded-xl border border-white/[0.1] bg-[#0b0e11]/80 px-3.5 pr-11 text-sm text-white outline-none transition placeholder:text-slate-700 hover:border-white/[0.16] focus:border-amber-400/70 focus:ring-4 focus:ring-amber-400/[0.08]';
    @endphp

    <div class="space-y-5">
        <div class="space-y-3">
            <div class="flex items-center justify-between gap-4">
                <div class="inline-flex items-center gap-2 rounded-full border border-emerald-400/15 bg-emerald-400/[0.06] px-2.5 py-1 text-[9px] font-bold uppercase tracking-[0.18em] text-emerald-400">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,.75)]"></span>
                    {{ __('Identity verified') }}
                </div>
                <span class="font-mono text-[9px] uppercase tracking-[0.18em] text-slate-600">{{ __('Step 3 of 3') }}</span>
            </div>

            <div>
                <h1 class="font-heading text-2xl font-black tracking-tight text-white sm:text-[28px]">{{ __('Set a new password') }}</h1>
                <p class="mt-1.5 text-xs leading-relaxed text-slate-400">
                    {{ __('Choose a new password for your account. You will use it the next time you sign in.') }}
                </p>
            </div>
        </div>

        @if ($recoveryEmail)
            <div class="flex items-center gap-3 rounded-xl border border-emerald-400/10 bg-emerald-400/[0.035] px-3.5 py-3">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-400/[0.08] text-emerald-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.62-4.02A11.96 11.96 0 0112 2.94a11.96 11.96 0 01-8.62 3.04A12.02 12.02 0 003 9c0 5.59 3.82 10.29 9 11.62 5.18-1.33 9-6.03 9-11.62 0-1.04-.13-2.05-.38-3.02z"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="text-[9px] font-bold uppercase tracking-[0.14em] text-slate-500">{{ __('Verified account') }}</div>
                    <div class="mt-0.5 truncate text-xs font-semibold text-slate-200">{{ $recoveryEmail }}</div>
                </div>
            </div>
        @endif

        <form action="{{ route('user.reset-password.update') }}" method="POST" class="ajax-form space-y-4" data-action="redirect">
            @csrf

            <div class="space-y-1.5">
                <label for="password" class="block text-[9px] font-bold uppercase tracking-[0.14em] text-slate-400">{{ __('New password') }}</label>
                <div class="relative">
                    <input type="password" name="password" id="password" class="{{ $fieldClass }}"
                        placeholder="••••••••" minlength="8" autocomplete="new-password" required autofocus>
                    <button type="button" data-password-toggle="password" aria-label="{{ __('Show password') }}"
                        class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-600 transition hover:text-amber-300 focus:outline-none focus:text-amber-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7s-8.27-2.94-9.54-7z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-[9px] font-bold uppercase tracking-[0.14em] text-slate-400">{{ __('Confirm new password') }}</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="{{ $fieldClass }}"
                        placeholder="••••••••" minlength="8" autocomplete="new-password" required>
                    <button type="button" data-password-toggle="password_confirmation" aria-label="{{ __('Show password') }}"
                        class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-600 transition hover:text-amber-300 focus:outline-none focus:text-amber-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7s-8.27-2.94-9.54-7z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>
                    </button>
                </div>
                <p id="password-match-status" class="hidden text-[10px] leading-4" aria-live="polite"></p>
            </div>

            <div class="rounded-xl border border-white/[0.07] bg-white/[0.02] px-3.5 py-3">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[9px] font-bold uppercase tracking-[0.12em] text-slate-500">{{ __('Password requirement') }}</span>
                    <span id="length-status" class="font-mono text-[9px] text-slate-600">{{ __('At least 8 characters') }}</span>
                </div>
                <div class="mt-2 h-1 overflow-hidden rounded-full bg-white/[0.06]">
                    <div id="password-progress" class="h-full w-0 rounded-full bg-amber-400 transition-all duration-300"></div>
                </div>
            </div>

            <button type="submit"
                class="group relative w-full cursor-pointer overflow-hidden rounded-xl bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 px-4 py-3.5 text-sm font-extrabold text-black shadow-[0_0_30px_rgba(247,166,0,0.2)] transition duration-300 hover:-translate-y-0.5 hover:from-amber-300 hover:to-amber-500 focus:outline-none focus:ring-4 focus:ring-amber-400/20">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <span>{{ __('Update & Secure Password') }}</span>
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </span>
            </button>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirmation = document.getElementById('password_confirmation');
            const progress = document.getElementById('password-progress');
            const lengthStatus = document.getElementById('length-status');
            const matchStatus = document.getElementById('password-match-status');

            document.querySelectorAll('[data-password-toggle]').forEach(function(button) {
                button.addEventListener('click', function() {
                    const input = document.getElementById(this.dataset.passwordToggle);
                    const isHidden = input.type === 'password';
                    input.type = isHidden ? 'text' : 'password';
                    this.setAttribute('aria-label', isHidden ? @json(__('Hide password')) : @json(__('Show password')));
                });
            });

            function updatePasswordFeedback() {
                const length = password.value.length;
                const percentage = Math.min((length / 8) * 100, 100);
                progress.style.width = percentage + '%';
                progress.classList.toggle('bg-emerald-400', length >= 8);
                progress.classList.toggle('bg-amber-400', length < 8);
                lengthStatus.textContent = length >= 8 ? @json(__('Requirement met')) : @json(__('At least 8 characters'));
                lengthStatus.className = length >= 8 ? 'font-mono text-[9px] text-emerald-400' : 'font-mono text-[9px] text-slate-600';

                if (confirmation.value.length === 0) {
                    matchStatus.classList.add('hidden');
                    return;
                }

                const matches = password.value === confirmation.value;
                matchStatus.textContent = matches ? @json(__('Passwords match')) : @json(__('Passwords do not match yet'));
                matchStatus.className = matches ? 'text-[10px] leading-4 text-emerald-400' : 'text-[10px] leading-4 text-rose-400';
            }

            password.addEventListener('input', updatePasswordFeedback);
            confirmation.addEventListener('input', updatePasswordFeedback);
        });
    </script>
@endsection
