@extends('templates.york.blades.layouts.user')

@section('content')
    @php
        $currency = getSetting('currency');
        $decimalPlaces = (int) getSetting('decimal_places', 2);
        $statusData = match ($deposit->status) {
            'pending' => [__('Pending'), 'border-amber-400/15 bg-amber-400/[0.07] text-amber-400', 'bg-amber-400', __('Awaiting blockchain confirmation or account review.')],
            'completed' => [__('Completed'), 'border-emerald-400/15 bg-emerald-400/[0.07] text-emerald-400', 'bg-emerald-400', __('This deposit has been confirmed and credited.')],
            'failed' => [__('Failed'), 'border-rose-400/15 bg-rose-400/[0.07] text-rose-400', 'bg-rose-400', __('This deposit was not completed successfully.')],
            'partial_payment' => [__('Partial Payment'), 'border-orange-400/15 bg-orange-400/[0.07] text-orange-400', 'bg-orange-400', __('The received amount was lower than the expected deposit total.')],
            default => [__(str_replace('_', ' ', $deposit->status)), 'border-white/[0.08] bg-white/[0.03] text-slate-400', 'bg-slate-400', __('The deposit status was updated.')],
        };
        $explorerUrl = $deposit->explorerTxUrl();
        $rawStructuredData = $deposit->getAttributes()['structured_data'] ?? null;
        $details = is_array($rawStructuredData) ? $rawStructuredData : (is_string($rawStructuredData) ? json_decode($rawStructuredData, true) : null);
    @endphp

    <div class="relative min-h-screen pb-12">
        <div class="pointer-events-none fixed right-0 top-0 -z-0 h-[38rem] w-[38rem] -translate-y-1/3 translate-x-1/3 rounded-full bg-amber-500/[0.04] blur-[140px]"></div>

        <div class="relative z-10 mx-auto max-w-6xl space-y-4">
            <header class="rounded-2xl border border-white/[0.08] bg-[#111318] p-5 sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="flex min-w-0 items-start gap-3">
                        <a href="{{ route('user.deposits.index') }}" aria-label="{{ __('Back to deposits') }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/[0.08] bg-white/[0.03] text-slate-400 transition hover:bg-white/[0.06] hover:text-white focus:outline-none focus:ring-2 focus:ring-amber-400/30"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
                        <div class="min-w-0">
                            <div class="mb-1.5 flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.18em] text-amber-400"><span class="h-1.5 w-1.5 rounded-full {{ $statusData[2] }}"></span>{{ __('Deposit record') }}</div>
                            <h1 class="font-heading text-2xl font-black tracking-tight text-white sm:text-3xl">{{ __('Deposit Details') }}</h1>
                            <p class="mt-1 truncate font-mono text-[10px] text-slate-500" title="{{ $deposit->transaction_reference }}">{{ $deposit->transaction_reference }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        @if (in_array($deposit->status, ['completed', 'partial_payment'], true))
                            <a href="{{ route('user.deposits.receipt', $deposit->transaction_reference) }}" class="flex min-h-10 items-center justify-center gap-2 rounded-xl border border-white/[0.09] bg-white/[0.035] px-4 text-[10px] font-bold uppercase tracking-wider text-slate-200 transition hover:bg-white/[0.07] focus:outline-none focus:ring-2 focus:ring-amber-400/30"><svg class="h-3.5 w-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-4-4m4 4l4-4M5 20h14"/></svg>{{ __('Download Receipt') }}</a>
                        @endif
                        <span class="inline-flex min-h-10 items-center justify-center rounded-xl border px-4 text-[10px] font-black uppercase tracking-wider {{ $statusData[1] }}">{{ $statusData[0] }}</span>
                    </div>
                </div>
            </header>

            <section class="rounded-2xl border border-white/[0.08] bg-[#111318] p-4 sm:p-5" aria-label="{{ __('Deposit status') }}">
                <div class="flex items-start gap-3"><span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border {{ $statusData[1] }}"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">@if ($deposit->status === 'completed')<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>@elseif ($deposit->status === 'failed')<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>@else<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>@endif</svg></span><div><h2 class="text-xs font-bold text-slate-200">{{ $statusData[0] }}</h2><p class="mt-1 text-[10px] leading-4 text-slate-500">{{ $statusData[3] }}</p></div></div>
            </section>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                <main class="space-y-4 lg:col-span-8">
                    <section class="overflow-hidden rounded-2xl border border-white/[0.08] bg-[#111318]">
                        <div class="border-b border-white/[0.07] px-5 py-4"><h2 class="text-sm font-black text-white">{{ __('Payment Summary') }}</h2><p class="mt-1 text-[10px] text-slate-500">{{ __('Recorded amounts and conversion values') }}</p></div>
                        <div class="grid grid-cols-1 divide-y divide-white/[0.06] sm:grid-cols-3 sm:divide-x sm:divide-y-0 sm:divide-white/[0.06]">
                            <div class="p-4"><p class="text-[9px] font-bold uppercase tracking-wider text-slate-600">{{ __('Requested Amount') }}</p><p class="mt-2 font-mono text-lg font-black text-white">{{ number_format($deposit->amount, $decimalPlaces) }} <span class="text-[9px] text-slate-600">{{ $currency }}</span></p></div>
                            <div class="p-4"><p class="text-[9px] font-bold uppercase tracking-wider text-slate-600">{{ __('Fee') }} · {{ number_format($deposit->fee_percent, 2) }}%</p><p class="mt-2 font-mono text-lg font-black text-rose-400">+{{ number_format($deposit->fee_amount, $decimalPlaces) }} <span class="text-[9px] text-slate-600">{{ $currency }}</span></p></div>
                            <div class="p-4"><p class="text-[9px] font-bold uppercase tracking-wider text-slate-600">{{ __('Total Payable') }}</p><p class="mt-2 font-mono text-lg font-black text-amber-400">{{ number_format($deposit->total_amount, $decimalPlaces) }} <span class="text-[9px] text-slate-600">{{ $currency }}</span></p></div>
                        </div>

                        @if ((float) $deposit->exchange_rate !== 1.0 || ($deposit->currency && $deposit->currency !== $currency))
                            <div class="grid grid-cols-1 gap-px border-t border-white/[0.07] bg-white/[0.06] sm:grid-cols-2">
                                <div class="bg-[#111318] p-4"><p class="text-[9px] font-bold uppercase tracking-wider text-slate-600">{{ __('Exchange Rate') }}</p><p class="mt-1.5 font-mono text-xs font-semibold text-slate-200">1 {{ $currency }} = {{ number_format($deposit->exchange_rate, 8) }} {{ $deposit->currency }}</p></div>
                                <div class="bg-[#111318] p-4"><p class="text-[9px] font-bold uppercase tracking-wider text-slate-600">{{ __('Converted Amount') }}</p><p class="mt-1.5 font-mono text-xs font-bold text-amber-400">{{ number_format($deposit->converted_amount, 8) }} {{ $deposit->currency }}</p></div>
                            </div>
                        @endif
                    </section>

                    <section class="overflow-hidden rounded-2xl border border-white/[0.08] bg-[#111318]">
                        <div class="border-b border-white/[0.07] px-5 py-4"><h2 class="text-sm font-black text-white">{{ __('Network Details') }}</h2><p class="mt-1 text-[10px] text-slate-500">{{ __('Blockchain source and transaction identifiers') }}</p></div>
                        <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2">
                            <div class="rounded-xl border border-white/[0.06] bg-white/[0.02] p-3.5"><p class="text-[9px] font-bold uppercase tracking-wider text-slate-600">{{ __('Deposit Source') }}</p><div class="mt-2 flex items-center gap-2">@if ($deposit->blockchainToken)<span class="flex h-7 w-7 items-center justify-center overflow-hidden rounded-lg border border-white/[0.08] bg-white/[0.03]"><img src="{{ $deposit->blockchainToken->logo ? asset($deposit->blockchainToken->logo) : asset('assets/images/tokens/' . strtolower($deposit->blockchainToken->symbol) . '.png') }}" alt="" class="h-5 w-5 object-contain" onerror="this.style.display='none'"></span>@endif<span class="text-xs font-bold text-slate-200">{{ $deposit->gatewayName() }}</span></div></div>
                            <div class="rounded-xl border border-white/[0.06] bg-white/[0.02] p-3.5"><p class="text-[9px] font-bold uppercase tracking-wider text-slate-600">{{ __('Reference') }}</p><div class="mt-2 flex items-center gap-2"><code class="min-w-0 flex-1 truncate font-mono text-[10px] text-slate-300" title="{{ $deposit->transaction_reference }}">{{ $deposit->transaction_reference }}</code><button type="button" class="copy-btn flex h-7 w-7 shrink-0 cursor-pointer items-center justify-center rounded-lg border border-white/[0.08] text-slate-500 transition hover:text-white" data-clipboard-text="{{ $deposit->transaction_reference }}" aria-label="{{ __('Copy deposit reference') }}"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 8h11v11H8zM5 16H4V5h11v1"/></svg></button></div></div>
                        </div>

                        @if ($deposit->transaction_hash)
                            <div class="border-t border-white/[0.06] p-5"><div class="flex items-center justify-between gap-3"><p class="text-[9px] font-bold uppercase tracking-wider text-slate-600">{{ __('Transaction Hash') }}</p>@if ($explorerUrl)<a href="{{ $explorerUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-[9px] font-bold uppercase tracking-wider text-amber-400 transition hover:text-amber-300">{{ __('View Explorer') }}<svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7v7m0-7L10 14M5 7v14h14v-5"/></svg></a>@endif</div><div class="mt-2 flex items-start gap-2 rounded-xl border border-white/[0.06] bg-black/20 p-3"><code class="min-w-0 flex-1 break-all font-mono text-[10px] leading-5 text-slate-300">{{ $deposit->transaction_hash }}</code><button type="button" class="copy-btn flex h-7 w-7 shrink-0 cursor-pointer items-center justify-center rounded-lg border border-white/[0.08] text-slate-500 transition hover:text-white" data-clipboard-text="{{ $deposit->transaction_hash }}" aria-label="{{ __('Copy transaction hash') }}"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 8h11v11H8zM5 16H4V5h11v1"/></svg></button></div></div>
                        @endif

                        @if ($details && is_array($details))
                            <div class="border-t border-white/[0.06] p-5"><h3 class="mb-3 text-[9px] font-bold uppercase tracking-wider text-slate-600">{{ __('Additional Details') }}</h3><dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @foreach ($details as $key => $value)
                                    @if (!is_array($value) && $key !== 'transaction_hash')<div class="rounded-lg bg-white/[0.02] p-3"><dt class="text-[9px] uppercase tracking-wider text-slate-600">{{ __(ucwords(str_replace('_', ' ', $key))) }}</dt><dd class="mt-1 break-all text-xs font-semibold text-slate-300">{{ $value }}</dd></div>@endif
                                @endforeach
                            </dl></div>
                        @endif
                    </section>
                </main>

                <aside class="space-y-4 lg:col-span-4">
                    <section class="rounded-2xl border border-white/[0.08] bg-[#111318] p-5">
                        <h2 class="text-sm font-black text-white">{{ __('Timeline') }}</h2>
                        <div class="relative mt-5 ml-1 border-l border-white/[0.09] pl-5">
                            <div class="relative pb-7"><span class="absolute -left-[25px] top-0.5 h-2 w-2 rounded-full bg-amber-400 ring-4 ring-[#111318]"></span><p class="text-[9px] font-bold uppercase tracking-wider text-slate-600">{{ __('Created') }}</p><p class="mt-1 text-xs font-semibold text-slate-200">{{ $deposit->created_at->format('M d, Y · H:i') }}</p></div>
                            @if ($deposit->status === 'pending' && $deposit->expires_at)
                                <div class="relative"><span class="absolute -left-[25px] top-0.5 h-2 w-2 rounded-full bg-amber-400 ring-4 ring-[#111318]"></span><p class="text-[9px] font-bold uppercase tracking-wider text-slate-600">{{ __('Expires') }}</p><p class="mt-1 text-xs font-semibold text-amber-400">{{ \Carbon\Carbon::createFromTimestamp($deposit->expires_at)->format('M d, Y · H:i') }}</p><p id="countdown-timer" data-expires="{{ $deposit->expires_at }}" class="mt-1 font-mono text-[10px] text-slate-500"><span id="timer-display">--:--:--</span> {{ __('remaining') }}</p></div>
                            @else
                                <div class="relative"><span class="absolute -left-[25px] top-0.5 h-2 w-2 rounded-full {{ $statusData[2] }} ring-4 ring-[#111318]"></span><p class="text-[9px] font-bold uppercase tracking-wider text-slate-600">{{ __('Last Updated') }}</p><p class="mt-1 text-xs font-semibold text-slate-200">{{ $deposit->updated_at->format('M d, Y · H:i') }}</p></div>
                            @endif
                        </div>
                    </section>

                    <section class="rounded-2xl border border-amber-400/10 bg-amber-400/[0.035] p-5"><div class="flex items-start gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-400/[0.08] text-amber-400"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span><div><h2 class="text-xs font-bold text-slate-200">{{ __('Need help with this deposit?') }}</h2><p class="mt-1 text-[10px] leading-4 text-slate-500">{{ __('Contact support and include the deposit reference so the transaction can be identified quickly.') }}</p><a href="{{ route('contact') }}" class="mt-3 inline-flex text-[9px] font-bold uppercase tracking-wider text-amber-400 hover:text-amber-300">{{ __('Contact Support') }} →</a></div></div></section>
                </aside>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.copy-btn').forEach(function(button) {
                button.addEventListener('click', async function() {
                    const value = this.dataset.clipboardText;
                    try {
                        if (navigator.clipboard?.writeText) await navigator.clipboard.writeText(value);
                        else {
                            const field = document.createElement('textarea');
                            field.value = value; field.style.position = 'fixed'; field.style.opacity = '0'; document.body.appendChild(field); field.select();
                            if (!document.execCommand('copy')) throw new Error('Copy failed');
                            field.remove();
                        }
                        if (typeof toastNotification === 'function') toastNotification({{ Illuminate\Support\Js::from(__('Copied to clipboard')) }}, 'success');
                    } catch (error) {
                        if (typeof toastNotification === 'function') toastNotification({{ Illuminate\Support\Js::from(__('Could not copy. Please copy it manually.')) }}, 'error');
                    }
                });
            });

            const timer = document.getElementById('countdown-timer');
            const display = document.getElementById('timer-display');
            if (timer && display) {
                const expiresAt = Number(timer.dataset.expires) * 1000;
                const updateCountdown = function() {
                    const distance = expiresAt - Date.now();
                    if (distance <= 0) { display.textContent = {{ Illuminate\Support\Js::from(__('Expired')) }}; display.classList.add('text-rose-400'); return false; }
                    const hours = String(Math.floor(distance / 3600000)).padStart(2, '0');
                    const minutes = String(Math.floor((distance % 3600000) / 60000)).padStart(2, '0');
                    const seconds = String(Math.floor((distance % 60000) / 1000)).padStart(2, '0');
                    display.textContent = `${hours}:${minutes}:${seconds}`;
                    return true;
                };
                updateCountdown();
                const interval = setInterval(function() { if (!updateCountdown()) clearInterval(interval); }, 1000);
            }
        });
    </script>
@endsection
