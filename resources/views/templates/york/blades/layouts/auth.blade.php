<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ config('languages.' . app()->getLocale() . '.rtl') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page_title ?? 'User Portal' }} | {{ getSetting('name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/' . getSetting('favicon')) }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0b0e11">
    <meta name="author" content="{{ getSetting('name') }} Team">
    <meta name="publisher" content="{{ getSetting('name') }} Team">

    @if (config('site.use_vite'))
        @vite(['resources/views/templates/york/css/app.css'])
    @else
        <link rel="stylesheet"
            href="{{ asset('assets/templates/york/css/main.css' . '?v=' . filemtime(public_path('assets/templates/york/css/main.css'))) }}">
    @endif
    {!! getSetting('header_scripts') !!}
</head>

<body class="bg-[#0b0e11] text-slate-200 font-sans antialiased min-h-screen relative isolate overflow-x-hidden flex flex-col justify-between selection:bg-amber-500 selection:text-black">
    {{-- Animated Preloader --}}
    @include('templates.york.blades.partials.dashoard-preloader')

    {{-- Global Ambient Glows & Grid Pattern --}}
    <div class="fixed inset-0 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:32px_32px] opacity-[0.025] pointer-events-none -z-20"></div>
    <div class="fixed top-[-10%] left-1/2 -translate-x-1/2 w-[700px] h-[500px] bg-amber-500/10 rounded-full blur-[180px] pointer-events-none -z-10 animate-pulse" style="animation-duration: 8s;"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-orange-600/10 rounded-full blur-[180px] pointer-events-none -z-10 animate-pulse" style="animation-duration: 10s;"></div>

    @php
        $logo = getSetting('logo_rectangle');
        $logoPath = public_path('assets/images/' . $logo);
        $version = $logo && file_exists($logoPath) ? filemtime($logoPath) : time();
    @endphp

    {{-- Top Bar --}}
    <div class="fixed top-5 inset-x-0 max-w-7xl mx-auto px-4 sm:px-8 flex items-center justify-between z-50 pointer-events-none">
        @hasSection('auth_showcase')
            <a href="{{ route('home') }}"
                class="pointer-events-auto flex items-center justify-start transition hover:opacity-90">
                @if ($logo)
                    <img src="{{ asset('assets/images/' . $logo) . '?v=' . $version }}"
                        alt="{{ getSetting('name') }}" class="h-8 w-auto max-w-[145px] object-contain sm:h-9 sm:max-w-[180px]">
                @else
                    <span class="font-heading text-xl font-black tracking-tight text-white">{{ getSetting('name') }}<span class="text-amber-400">.</span></span>
                @endif
            </a>
        @else
        <a href="{{ route('home') }}" class="pointer-events-auto inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.03] hover:bg-white/[0.08] border border-white/[0.08] text-slate-400 hover:text-white text-xs font-mono transition-all backdrop-blur-xl group shadow-lg">
            <svg class="w-3.5 h-3.5 transform group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span class="hidden sm:inline">{{ __('Return to Site') }}</span>
        </a>
        @endif

        @hasSection('auth_showcase')
        <div class="pointer-events-auto flex items-center gap-2 sm:gap-3">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-full border border-white/[0.08] bg-white/[0.03] px-3 py-1.5 font-mono text-xs text-slate-400 shadow-lg backdrop-blur-xl transition hover:bg-white/[0.08] hover:text-white">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span class="hidden md:inline">{{ __('Return to Site') }}</span>
            </a>
        @endif

        {{-- Language Switcher Dropdown --}}
        <div class="pointer-events-auto relative group" id="lang-switcher">
            <button id="lang-btn"
                class="flex items-center gap-2 text-slate-400 hover:text-white transition-all bg-white/[0.03] hover:bg-white/[0.08] border border-white/[0.08] hover:border-amber-500/30 rounded-full px-3.5 py-1.5 backdrop-blur-xl cursor-pointer text-xs font-mono shadow-lg"
                onclick="toggleLanguageMenu(event)">
                @php
                    $locale = app()->getLocale();
                    $languages = config('languages');
                    $currentLang = $languages[$locale] ?? ['name' => $locale, 'flag' => 'us'];
                @endphp
                <img src="{{ asset('assets/flags/' . $currentLang['flag'] . '.svg') }}"
                    alt="{{ $currentLang['name'] }}" class="w-3.5 h-3.5 rounded-full object-cover">
                <span class="font-medium hidden sm:inline-block">{{ $currentLang['name'] }}</span>
                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div id="lang-menu"
                class="absolute right-0 top-full mt-2 w-40 bg-[#151a21]/95 backdrop-blur-2xl border border-white/10 rounded-2xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right overflow-hidden z-50 p-1 font-mono text-xs">
                @foreach ($languages as $code => $lang)
                    <a href="{{ route('lang.switch', $code) }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/10 transition-colors {{ app()->getLocale() == $code ? 'bg-amber-500/10 text-amber-400 font-bold' : '' }}">
                        <img src="{{ asset('assets/flags/' . $lang['flag'] . '.svg') }}" alt="{{ $lang['name'] }}"
                            class="w-3.5 h-3.5 rounded-full object-cover">
                        <span>{{ $lang['name'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        @hasSection('auth_showcase')
        </div>
        @endif
    </div>

    {{-- Authentication Workspace --}}
    <div class="{{ View::hasSection('auth_showcase') ? 'max-w-7xl lg:grid lg:grid-cols-[minmax(0,1.35fr)_minmax(400px,460px)] lg:items-center lg:gap-10 xl:gap-16' : 'max-w-[460px]' }} w-full mx-auto my-auto relative z-10 pt-20 pb-10 px-4 sm:px-6">
        @hasSection('auth_showcase')
            <aside class="hidden lg:block min-w-0">
                @yield('auth_showcase')
            </aside>
        @endif

        <div class="w-full {{ View::hasSection('auth_showcase') ? 'max-w-[460px] lg:justify-self-end' : '' }}">
        @unless(View::hasSection('auth_showcase'))
        {{-- Brand Header for centered authentication pages --}}
        <div class="mb-8 text-center">
            <a href="{{ route('home') }}" class="inline-block mb-3 group">
                <div class="inline-flex items-center justify-center p-3 rounded-2xl bg-white/[0.02] border border-white/[0.08] group-hover:border-amber-500/30 transition-all backdrop-blur-xl shadow-inner">
                    @if ($logo)
                        <img src="{{ asset('assets/images/' . $logo) . '?v=' . $version }}"
                            alt="{{ getSetting('name') }}" class="h-9 w-auto object-contain">
                    @else
                        <span class="text-2xl font-mono font-bold text-white tracking-tight">{{ getSetting('name') }}</span>
                    @endif
                </div>
            </a>
        </div>
        @endunless

        {{-- Double-Bezel Glass Card --}}
        <div class="p-2 rounded-[2.5rem] bg-white/[0.02] border border-white/[0.1] backdrop-blur-2xl shadow-[0_25px_70px_rgba(0,0,0,0.9)] relative overflow-hidden group">
            {{-- Laser Top Border Accent --}}
            <div class="absolute top-0 inset-x-10 h-[1.5px] bg-gradient-to-r from-transparent via-amber-400/60 to-transparent"></div>

            <div class="rounded-[2rem] bg-[#151a21]/95 shadow-[inset_0_1px_1px_rgba(255,255,255,0.15)] p-6 sm:p-8 relative">
                @yield('content')
            </div>
        </div>

        @unless(View::hasSection('auth_showcase'))
            <div class="mt-7">
                @include('templates.york.blades.partials.auth-footer')
            </div>
        @endunless
        </div>

    </div>

    @hasSection('auth_showcase')
        <footer class="relative z-10 mt-auto w-full border-t border-white/[0.06] bg-[#0b0e11]/70 px-4 py-5 backdrop-blur-xl">
            @include('templates.york.blades.partials.auth-footer')
        </footer>
    @else
        {{-- Spacer maintains vertical balance on centered auth screens. --}}
        <div></div>
    @endif

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (config('site.use_vite'))
        @vite(['resources/views/templates/york/js/app.js'])
    @else
        <script
            src="{{ asset('assets/templates/york/js/main.js' . '?v=' . filemtime(public_path('assets/templates/york/js/main.js'))) }}">
        </script>
    @endif

    <script>
        function toggleLanguageMenu(event) {
            event.stopPropagation();
            const menu = document.getElementById('lang-menu');
            if (menu.classList.contains('invisible')) {
                menu.classList.remove('invisible', 'opacity-0');
            } else {
                menu.classList.add('invisible', 'opacity-0');
            }
        }

        document.addEventListener('click', function(event) {
            const menu = document.getElementById('lang-menu');
            const btn = document.getElementById('lang-btn');
            if (menu && !menu.classList.contains('invisible') && btn && !btn.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.add('invisible', 'opacity-0');
            }
        });
    </script>

    @include('templates.york.blades.partials.notifications')
    @yield('scripts')

    {!! Blade::render(getSetting('livechat_scripts')) !!}
    {!! getSetting('footer_scripts') !!}
</body>

</html>
