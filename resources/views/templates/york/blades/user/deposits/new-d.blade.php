@extends('templates.york.blades.layouts.user')

@section('content')
@php $depositTokenGroups = array_values($token_blockchain_map); @endphp
<div class="min-h-screen relative space-y-4 pb-16">

    {{-- Global ambient background blur --}}
    <div class="fixed top-0 right-0 w-[40rem] h-[40rem] bg-accent-primary/5 rounded-full blur-[120px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
    <div class="fixed bottom-0 left-0 w-[30rem] h-[30rem] bg-orange-500/[0.03] rounded-full blur-[120px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>

    <div class="relative z-10 max-w-6xl mx-auto space-y-4">

        {{-- ══ HEADER DECK ══════════════════════════════════════════════════════ --}}
        <div class="relative rounded-2xl overflow-hidden border border-white/[0.08] bg-[#111318] p-5 sm:p-6">
            
            <div class="absolute top-0 right-0 w-80 h-80 bg-accent-primary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-5">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-accent-primary shadow-[0_0_8px_rgba(226,177,60,0.7)]"></span>
                        <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-amber-400">{{ __('On-chain funding') }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">
                        {{ __('Deposit Crypto') }}
                    </h1>
                    <p class="text-slate-400 text-xs leading-5 mt-1.5 font-medium max-w-2xl">
                        {{ __('Choose the asset first, then confirm the exact blockchain network before sending funds to your personal address.') }}
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 shrink-0">
                    <div class="inline-flex items-center gap-2 rounded-xl border border-emerald-400/10 bg-emerald-400/[0.035] px-4 py-2.5 text-xs font-bold text-slate-300">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        <span>{{ trans_choice(':count asset available|:count assets available', count($depositTokenGroups), ['count' => count($depositTokenGroups)]) }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if ($blockchains->isEmpty())
            <div class="relative rounded-2xl overflow-hidden p-12 text-center max-w-lg mx-auto"
                 style="background: linear-gradient(145deg, rgba(8,9,14,0.97) 0%, rgba(5,6,10,0.99) 100%); border: 1px solid rgba(255,255,255,0.06);">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-500 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">{{ __('No Blockchains Supported') }}</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                    {{ __('No deposit blockchains are currently configured or active. Please check back later.') }}
                </p>
            </div>
        @else
            {{-- ══ SEARCH & FILTER BAR ══════════════════════════════════════════════ --}}
            <div class="rounded-2xl border border-white/[0.08] bg-[#111318] p-4">
                <div class="relative flex items-center gap-3">
                    <input id="deposit-token-search"
                           type="search" aria-label="{{ __('Search deposit assets') }}"
                           placeholder="{{ __('Search asset or network') }}"
                           class="h-11 w-full bg-[#0b0e11] border border-white/[0.09] rounded-xl pl-11 pr-4 text-sm text-white placeholder:text-slate-600 focus:outline-none focus:border-accent-primary/60 focus:ring-4 focus:ring-amber-400/[0.08] transition-all font-medium">
                    <svg class="w-4 h-4 text-slate-600 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            {{-- ══ TOKEN CARDS LIST ═════════════════════════════════════════════════ --}}
            <div class="grid grid-cols-1 gap-3 lg:grid-cols-2" id="deposit-assets-grid">
                @foreach ($depositTokenGroups as $index => $tokenGroup)
                    @php $token = $tokenGroup['token']; @endphp
                    
                    <div class="deposit-token-card relative rounded-xl overflow-hidden border border-white/[0.07] bg-[#111318] transition-all duration-200 hover:border-white/[0.14]"
                         data-search="{{ strtolower($token['symbol']) }} {{ strtolower($token['name']) }} {{ collect($tokenGroup['blockchains'])->pluck('blockchain_code')->map(fn($code) => strtolower($code))->implode(' ') }}">
                        
                        {{-- Token Main Row --}}
                        <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-11 h-11 rounded-xl bg-accent-primary/10 border border-accent-primary/20 flex items-center justify-center text-accent-primary font-black text-xs shrink-0 overflow-hidden relative">
                                    @php
                                        $tokenLogo = !empty($token['logo']) ? asset($token['logo']) : asset('assets/images/tokens/' . strtolower($token['symbol']) . '.png');
                                    @endphp
                                    <img src="{{ $tokenLogo }}"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                         alt="{{ $token['symbol'] }}" class="w-full h-full object-contain p-2">
                                    <span class="hidden items-center justify-center w-full h-full font-black text-xs">{{ $token['symbol'] }}</span>
                                </div>
                                
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-black text-white tracking-tight">{{ $token['symbol'] }}</h3>
                                        <span class="truncate text-[9px] font-bold text-slate-500 uppercase tracking-wider bg-white/5 border border-white/5 px-2 py-0.5 rounded-md">
                                            {{ $token['name'] }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5 font-medium">
                                        {{ count($tokenGroup['blockchains']) }} {{ count($tokenGroup['blockchains']) == 1 ? __('Supported Network') : __('Supported Networks') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                <button type="button" data-token-index="{{ $index }}"
                                        class="select-network-button flex min-h-10 w-full items-center justify-center gap-2 rounded-xl border border-accent-primary/20 bg-accent-primary/10 px-4 text-[10px] font-black uppercase tracking-wider text-accent-primary transition hover:border-accent-primary hover:bg-accent-primary hover:text-black focus:outline-none focus:ring-4 focus:ring-amber-400/10 sm:w-auto cursor-pointer">
                                    <span>{{ __('Select Network') }}</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div id="deposit-search-empty" class="hidden rounded-2xl border border-dashed border-white/[0.09] bg-[#111318] p-10 text-center"><p class="text-sm font-semibold text-slate-300">{{ __('No matching deposit assets') }}</p><p class="mt-1 text-[10px] text-slate-600">{{ __('Try another asset symbol or network name.') }}</p></div>
        @endif

    </div>
</div>

{{-- ══ STEP 1: NETWORK SELECTION MODAL ════════════════════════════════════════ --}}
<div id="networkSelectionModal" class="hidden fixed inset-0 z-50 flex items-end justify-center bg-black/80 backdrop-blur-md p-0 sm:items-center sm:p-4" role="dialog" aria-modal="true" aria-labelledby="modalTokenTitle">
    <div class="relative flex max-h-[92dvh] w-full max-w-lg flex-col overflow-hidden rounded-t-2xl border border-white/[0.09] bg-[#11161c] shadow-2xl sm:rounded-2xl">
        
        {{-- Header --}}
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div id="modalTokenLogoContainer" class="w-10 h-10 rounded-xl bg-accent-primary/10 border border-accent-primary/20 flex items-center justify-center text-accent-primary font-black text-xs overflow-hidden shrink-0">
                    <img id="modalTokenLogo" src="" alt="" class="w-full h-full object-contain p-1.5">
                    <span id="modalTokenSymbolFallback" class="hidden font-black text-xs"></span>
                </div>
                <div>
                    <h3 id="modalTokenTitle" class="text-base font-black text-white tracking-tight"></h3>
                    <p class="text-xs text-slate-500 font-medium">{{ __('Choose a blockchain network for deposit') }}</p>
                </div>
            </div>

            <button type="button" onclick="closeNetworkSelectionModal()" aria-label="{{ __('Close network selection') }}" class="w-8 h-8 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white flex items-center justify-center transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-amber-400/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Network List Body --}}
        <div id="modalNetworkList" class="p-4 sm:p-6 space-y-3 overflow-y-auto overscroll-contain">
            {{-- Dynamically populated --}}
        </div>
    </div>
</div>

{{-- ══ STEP 2: DEPOSIT VAULT & QR CODE MODAL ══════════════════════════════════ --}}
<div id="depositVaultModal" class="hidden fixed inset-0 z-50 flex items-end justify-center bg-black/80 backdrop-blur-md p-0 sm:items-center sm:p-4" role="dialog" aria-modal="true" aria-labelledby="vaultModalHeaderTitle">
    <div class="relative max-h-[94dvh] w-full max-w-md overflow-y-auto overscroll-contain rounded-t-2xl border border-white/[0.09] bg-[#11161c] shadow-2xl sm:rounded-2xl">
        
        {{-- Vault Header --}}
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <button type="button" onclick="backToNetworkSelection()" class="p-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white transition-all mr-1 cursor-pointer focus:outline-none focus:ring-2 focus:ring-amber-400/30" title="{{ __('Back to networks') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div>
                    <h3 id="vaultModalHeaderTitle" class="text-base font-black text-white tracking-tight"></h3>
                    <p id="vaultModalHeaderSub" class="text-xs text-slate-500 font-medium"></p>
                </div>
            </div>

            <button type="button" onclick="closeDepositVaultModal()" aria-label="{{ __('Close deposit address') }}" class="w-8 h-8 rounded-xl bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white flex items-center justify-center transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-amber-400/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Vault Body --}}
        <div class="p-5 sm:p-6 space-y-5 text-center">
            
            {{-- QR Code Box --}}
            <div id="vaultQrContainer" class="flex flex-col items-center justify-center">
                <div class="p-3 bg-white rounded-2xl shadow-xl border border-white/10">
                    <img id="vaultQrImage" src="" alt="{{ __('Deposit address QR code') }}" class="w-36 h-36">
                </div>
                <p class="text-[10px] font-mono text-slate-500 mt-2">{{ __('Scan address with your mobile wallet app') }}</p>
            </div>

            {{-- Address Block or Generate CTA --}}
            <div id="vaultAddressContainer" class="space-y-3">
                <div class="flex items-center justify-between gap-3 text-left"><span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">{{ __('Personal Deposit Address') }}</span><span class="rounded-full border border-emerald-400/15 bg-emerald-400/[0.07] px-2 py-0.5 text-[8px] font-bold uppercase text-emerald-400">{{ __('Network specific') }}</span></div>
                
                <div class="p-3.5 rounded-xl bg-black/50 border border-white/8 font-mono text-xs text-white break-all text-left flex items-center justify-between gap-2 shadow-inner">
                    <span id="vaultAddressText" class="select-all"></span>
                </div>

                <button type="button" id="vaultCopyBtn" onclick="copyVaultAddress()"
                        class="w-full py-3 rounded-xl bg-accent-primary hover:bg-accent-primary/90 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_4px_20px_rgba(226,177,60,0.3)] active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span>{{ __('Copy Deposit Address') }}</span>
                </button>
            </div>

            <div id="vaultGenerateContainer" class="hidden space-y-3 py-4">
                <p class="text-xs text-slate-400">{{ __('No personal vault address has been generated for this network yet.') }}</p>
                <button type="button" id="vaultGenerateBtn"
                        class="w-full py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs uppercase tracking-wider transition-all shadow-[0_4px_20px_rgba(16,185,129,0.3)] active:scale-95 cursor-pointer">
                    {{ __('Generate Personal Address') }}
                </button>
            </div>

            {{-- Security Warning Card --}}
            <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-left space-y-1.5">
                <div class="flex items-center gap-2 text-amber-400 font-bold text-xs">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ __('Important Deposit Notice') }}</span>
                </div>
                <p id="vaultWarningText" class="text-[11px] text-slate-300 leading-relaxed font-medium">
                    {{ __('Send only the specified asset over this exact network. Transferring any other asset or using an incompatible network will result in loss of funds.') }}
                </p>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const depositTokenGroups = {{ Illuminate\Support\Js::from($depositTokenGroups) }};
let currentActiveTokenGroup = null;
let currentActiveNetwork = null;

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>'"]/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[character]));
}

function openNetworkSelectionModal(tokenGroup) {
    currentActiveTokenGroup = tokenGroup;
    const token = tokenGroup.token;
    
    // Set Header Info
    $('#modalTokenTitle').text(token.symbol + ' - ' + token.name);
    
    const logoUrl = token.logo ? ('/' + token.logo.replace(/^\//, '')) : ('/assets/images/tokens/' + token.symbol.toLowerCase() + '.png');
    $('#modalTokenLogo').attr('src', logoUrl).show();
    $('#modalTokenSymbolFallback').text(token.symbol).hide();

    // Populate Network List
    let html = '';
    tokenGroup.blockchains.forEach(function(net, networkIndex) {
        const netLogo = net.blockchain_logo ? ('/' + net.blockchain_logo.replace(/^\//, '')) : ('/assets/images/tokens/' + net.blockchain_code.toLowerCase() + '.png');
        const statusBadge = net.wallet_exists 
            ? '<span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-emerald-400"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>{{ __('Address active') }}</span>'
            : '<span class="inline-flex items-center gap-1.5 rounded-full border border-amber-500/20 bg-amber-500/10 px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-amber-400"><span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>{{ __('Setup required') }}</span>';

        html += `
            <button type="button" data-network-index="${networkIndex}"
                 class="network-option group flex w-full cursor-pointer items-center justify-between gap-4 rounded-xl border border-white/[0.06] bg-white/[0.02] p-4 text-left transition hover:border-accent-primary/40 hover:bg-white/[0.05] focus:outline-none focus:ring-2 focus:ring-amber-400/30">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0 group-hover:border-accent-primary/40 transition-all">
                        <img src="${escapeHtml(netLogo)}" alt="" onerror="this.style.display='none';" class="w-6 h-6 object-contain">
                    </div>
                    <div>
                        <div class="text-sm font-bold text-white group-hover:text-accent-primary transition-colors flex items-center gap-2">
                            <span>${escapeHtml(net.blockchain_network || net.blockchain_name)}</span>
                            <span class="text-[10px] font-mono text-slate-500">(${escapeHtml(net.blockchain_code.toUpperCase())})</span>
                        </div>
                        <div class="mt-1">${statusBadge}</div>
                    </div>
                </div>

                <div class="shrink-0 text-slate-400 group-hover:text-accent-primary transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </button>
        `;
    });

    $('#modalNetworkList').html(html);
    $('#networkSelectionModal').removeClass('hidden');
    $('body').addClass('overflow-hidden');
    $('#networkSelectionModal button').first().trigger('focus');
}

function closeNetworkSelectionModal() {
    $('#networkSelectionModal').addClass('hidden');
    if ($('#depositVaultModal').hasClass('hidden')) $('body').removeClass('overflow-hidden');
}

function selectNetworkAndOpenVault(network) {
    currentActiveNetwork = network;
    closeNetworkSelectionModal();

    const token = currentActiveTokenGroup.token;

    // Set Vault Header
    $('#vaultModalHeaderTitle').text(token.symbol + ' (' + network.blockchain_name + ')');
    $('#vaultModalHeaderSub').text('{{ __('Deposit via ') }}' + network.blockchain_name + ' (' + network.blockchain_code.toUpperCase() + ')');

    // Warning Text
    $('#vaultWarningText').text({{ Illuminate\Support\Js::from(__('Send only the selected asset through this exact network. Transfers made with an incompatible asset or network may be permanently lost.')) }} + ' ' + token.symbol + ' · ' + network.blockchain_name + ' (' + network.blockchain_code.toUpperCase() + ')');

    if (network.wallet_exists && network.wallet_address) {
        $('#vaultAddressText').text(network.wallet_address);
        $('#vaultQrImage').attr('src', 'https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=' + encodeURIComponent(network.wallet_address));
        $('#vaultQrContainer').removeClass('hidden');
        $('#vaultAddressContainer').removeClass('hidden');
        $('#vaultGenerateContainer').addClass('hidden');
    } else {
        $('#vaultQrContainer').addClass('hidden');
        $('#vaultAddressContainer').addClass('hidden');
        $('#vaultGenerateContainer').removeClass('hidden');
        
        $('#vaultGenerateBtn').off('click').on('click', function() {
            generatePersonalWallet(network.blockchain_id, network.blockchain_name);
        });
    }

    $('#depositVaultModal').removeClass('hidden');
    $('body').addClass('overflow-hidden');
    $('#depositVaultModal button').first().trigger('focus');
}

function backToNetworkSelection() {
    closeDepositVaultModal();
    if (currentActiveTokenGroup) {
        openNetworkSelectionModal(currentActiveTokenGroup);
    }
}

function closeDepositVaultModal() {
    $('#depositVaultModal').addClass('hidden');
    $('body').removeClass('overflow-hidden');
}

function copyVaultAddress() {
    const address = $('#vaultAddressText').text();
    if (!address) return;

    const copyPromise = navigator.clipboard?.writeText
        ? navigator.clipboard.writeText(address)
        : new Promise((resolve, reject) => {
            const field = document.createElement('textarea');
            field.value = address;
            field.style.position = 'fixed';
            field.style.opacity = '0';
            document.body.appendChild(field);
            field.select();
            document.execCommand('copy') ? resolve() : reject();
            field.remove();
        });

    copyPromise.then(function() {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ __('Deposit address copied to clipboard!') }}',
            showConfirmButton: false,
            timer: 2200,
            background: '#0f1217',
            color: '#fff'
        });
    }).catch(function() {
        if (typeof toastNotification === 'function') toastNotification({{ Illuminate\Support\Js::from(__('Could not copy the address. Please copy it manually.')) }}, 'error');
    });
}

$(function () {
    const $searchInput = $('#deposit-token-search');
    const $cards = $('.deposit-token-card');

    $searchInput.on('input', function () {
        const query = $(this).val().toLowerCase().trim();

        let visibleCount = 0;
        $cards.each(function () {
            const text = $(this).data('search') || '';
            const matches = text.includes(query);
            $(this).toggle(matches);
            if (matches) visibleCount++;
        });
        $('#deposit-search-empty').toggleClass('hidden', visibleCount > 0);
    });

    $(document).on('click', '.select-network-button', function() {
        openNetworkSelectionModal(depositTokenGroups[Number($(this).data('token-index'))]);
    });

    $(document).on('click', '.network-option', function() {
        const network = currentActiveTokenGroup?.blockchains?.[Number($(this).data('network-index'))];
        if (network) selectNetworkAndOpenVault(network);
    });

    $('#networkSelectionModal, #depositVaultModal').on('click', function(event) {
        if (event.target !== this) return;
        if (this.id === 'networkSelectionModal') closeNetworkSelectionModal();
        else closeDepositVaultModal();
    });

    $(document).on('keydown', function(event) {
        if (event.key !== 'Escape') return;
        if (!$('#depositVaultModal').hasClass('hidden')) closeDepositVaultModal();
        else if (!$('#networkSelectionModal').hasClass('hidden')) closeNetworkSelectionModal();
    });
});

function generatePersonalWallet(blockchainId, blockchainName) {
    Swal.fire({
        title: '{{ __('Generate Address?') }}',
        text: '{{ __('Create a personal deposit address for ') }}' + blockchainName + '{{ __('.') }}',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f7a600',
        cancelButtonColor: '#1e293b',
        confirmButtonText: '{{ __('Generate Now') }}',
        background: '#0f1217',
        color: '#fff',
        customClass: {
            popup: 'border border-white/10 rounded-2xl'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();
            $.ajax({
                url: '{{ route("user.deposits.generate-wallet") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    blockchain_id: blockchainId
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('Address Created!') }}',
                        text: response.message,
                        background: '#0f1217',
                        color: '#fff'
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    let msg = '{{ __('Failed to generate deposit address.') }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Notice') }}',
                        text: msg,
                        background: '#0f1217',
                        color: '#fff'
                    });
                }
            });
        }
    });
}
</script>
@endsection
