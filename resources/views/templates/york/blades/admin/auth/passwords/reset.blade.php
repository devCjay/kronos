@extends('templates.york.blades.admin.layouts.auth')

@section('content')
    <div class="space-y-6">
        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-block mb-3 group">
                <div class="inline-flex items-center justify-center p-2.5 rounded-2xl bg-white/[0.02] border border-white/[0.08] group-hover:border-amber-500/30 transition-all backdrop-blur-xl shadow-inner">
                    @php
                        $logo = getSetting('logo_rectangle');
                    @endphp
                    @if ($logo)
                        <img src="{{ asset('assets/images/' . $logo) }}" alt="{{ getSetting('name') }}"
                            class="h-8 w-auto object-contain">
                    @else
                        <span class="text-lg font-mono font-bold text-white tracking-tight">{{ getSetting('name') }}<span class="text-amber-400">_ADMIN</span></span>
                    @endif
                </div>
            </a>
            <div class="flex items-center justify-center mb-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 font-mono text-[9px] font-bold uppercase tracking-widest">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    {{ __('ADMINISTRATOR CONSOLE') }}
                </span>
            </div>
            <h2 class="text-xl font-bold text-white tracking-tight">{{ __('Set New Password') }}</h2>
            <p class="text-slate-400 text-xs mt-1 font-body">
                {{ __('Choose a strong new password for your administrator account.') }}
            </p>
        </div>

        <form action="{{ route('admin.reset-password.update') }}" method="POST" class="ajax-form space-y-4"
            data-action="redirect">
            @csrf

            {{-- Password --}}
            <div class="space-y-1.5">
                <label for="password" class="block text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">
                    {{ __('New Password') }}
                </label>
                <div class="relative">
                    <input type="password" name="password" id="password"
                        class="w-full bg-[#0b0e11]/80 border border-white/[0.1] rounded-2xl px-4 py-3.5 text-white placeholder-slate-600 focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition-all font-body text-sm shadow-inner"
                        placeholder="••••••••" required autofocus>
                </div>
            </div>

            {{-- Password Confirmation --}}
            <div class="space-y-1.5">
                <label for="password_confirmation"
                    class="block text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">
                    {{ __('Confirm New Password') }}
                </label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full bg-[#0b0e11]/80 border border-white/[0.1] rounded-2xl px-4 py-3.5 text-white placeholder-slate-600 focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition-all font-body text-sm shadow-inner"
                        placeholder="••••••••" required>
                </div>
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                class="w-full bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 hover:from-amber-300 hover:to-amber-500 text-black font-extrabold py-3.5 px-4 rounded-2xl shadow-[0_0_30px_rgba(247,166,0,0.25)] transition-all duration-300 transform hover:-translate-y-0.5 relative overflow-hidden group cursor-pointer text-sm font-mono tracking-wide mt-2">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <span>{{ __('Update & Secure Password') }}</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
                <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </button>
        </form>
    </div>
@endsection
