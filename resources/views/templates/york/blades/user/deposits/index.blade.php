@extends('templates.york.blades.layouts.user')

@section('content')
    @php
        $currency = getSetting('currency');
        $decimalPlaces = (int) getSetting('decimal_places', 2);
        $totalDeposited = (float) ($deposits_analytics['total']['total'] ?? 0);
        $pendingTotal = (float) ($deposits_analytics['pending']['total'] ?? 0);
        $completedTotal = (float) ($deposits_analytics['completed']['total'] ?? 0);
        $exceptionTotal = (float) ($deposits_analytics['failed']['total'] ?? 0) + (float) ($deposits_analytics['partial_payment']['total'] ?? 0);
    @endphp

    <div class="relative min-h-screen pb-12">
        <div class="pointer-events-none fixed right-0 top-0 -z-0 h-[38rem] w-[38rem] -translate-y-1/3 translate-x-1/3 rounded-full bg-amber-500/[0.04] blur-[140px]"></div>

        <div class="relative z-10 mx-auto max-w-7xl space-y-4">
            <header class="rounded-2xl border border-white/[0.08] bg-[#111318] p-5 sm:p-6">
                <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                    <div>
                        <div class="mb-2 flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.2em] text-amber-400">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-400 shadow-[0_0_8px_rgba(247,166,0,.75)]"></span>
                            {{ __('Funding activity') }}
                        </div>
                        <h1 class="font-heading text-2xl font-black tracking-tight text-white sm:text-3xl">{{ __('Deposit History') }}</h1>
                        <p class="mt-1.5 max-w-2xl text-xs leading-5 text-slate-400">{{ __('Track deposit references, blockchain confirmations, converted amounts, and processing status.') }}</p>
                    </div>
                    <a href="{{ route('user.deposits.new') }}" class="flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-amber-400 px-5 text-xs font-black uppercase tracking-wider text-black transition hover:bg-amber-300 focus:outline-none focus:ring-4 focus:ring-amber-400/20 sm:w-auto">
                        <span class="text-base leading-none">+</span>{{ __('New Deposit') }}
                    </a>
                </div>
            </header>

            <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="{{ __('Deposit summary') }}">
                @foreach ([
                    [__('All Deposits'), $totalDeposited, $deposits_analytics['total']['count'], 'text-white', __('records')],
                    [__('Pending'), $pendingTotal, $deposits_analytics['pending']['count'], 'text-amber-400', __('processing')],
                    [__('Completed'), $completedTotal, $deposits_analytics['completed']['count'], 'text-emerald-400', __('confirmed')],
                    [__('Failed / Partial'), $exceptionTotal, $deposits_analytics['failed']['count'] + $deposits_analytics['partial_payment']['count'], 'text-rose-400', __('requires attention')],
                ] as [$label, $amount, $count, $color, $caption])
                    <article class="rounded-xl border border-white/[0.07] bg-[#111318] p-4">
                        <p class="text-[9px] font-bold uppercase tracking-[0.15em] text-slate-500">{{ $label }}</p>
                        <p class="mt-2 break-all font-mono text-xl font-black {{ $color }}">{{ number_format($amount, $decimalPlaces) }} <span class="text-[9px] font-semibold text-slate-600">{{ $currency }}</span></p>
                        <p class="mt-1 text-[10px] text-slate-600">{{ $count }} {{ $caption }}</p>
                    </article>
                @endforeach
            </section>

            @if ($method_stats->isNotEmpty())
                <section class="rounded-2xl border border-white/[0.08] bg-[#111318] p-4 sm:p-5">
                    <div class="flex items-center justify-between gap-4">
                        <div><h2 class="text-sm font-black text-white">{{ __('Deposit Sources') }}</h2><p class="mt-1 text-[10px] text-slate-500">{{ __('Volume distribution by blockchain or payment source') }}</p></div>
                        @if ($largest_deposit)
                            <div class="hidden text-right sm:block"><p class="text-[9px] uppercase tracking-wider text-slate-600">{{ __('Largest Deposit') }}</p><p class="mt-1 font-mono text-xs font-bold text-white">{{ number_format($largest_deposit->amount, $decimalPlaces) }} {{ $currency }}</p></div>
                        @endif
                    </div>

                    <div id="methods-container" class="mt-4 grid grid-cols-1 gap-2.5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($method_stats as $stat)
                            @php $percent = $totalDeposited > 0 ? min(100, ((float) $stat->total / $totalDeposited) * 100) : 0; @endphp
                            <article class="method-item rounded-xl border border-white/[0.06] bg-white/[0.02] p-3.5 {{ $loop->index > 5 ? 'hidden' : '' }}">
                                <div class="flex items-center justify-between gap-3"><div class="flex min-w-0 items-center gap-2"><span class="h-2 w-2 shrink-0 rounded-full bg-amber-400"></span><h3 class="truncate text-xs font-bold text-slate-200">{{ $stat->gatewayName() }}</h3></div><span class="font-mono text-[10px] font-bold text-amber-400">{{ number_format($percent, 1) }}%</span></div>
                                <div class="mt-2 flex items-center justify-between text-[10px]"><span class="text-slate-600">{{ $stat->count }} {{ __('deposits') }}</span><span class="font-mono font-semibold text-slate-300">{{ number_format($stat->total, $decimalPlaces) }} {{ $currency }}</span></div>
                                <div class="mt-2 h-1 overflow-hidden rounded-full bg-white/[0.06]"><div class="h-full rounded-full bg-amber-400" style="width: {{ $percent }}%"></div></div>
                            </article>
                        @endforeach
                    </div>

                    @if ($method_stats->count() > 6)
                        <div class="mt-4 border-t border-white/[0.06] pt-3 text-center"><button type="button" data-target="#methods-container" class="toggle-load-more cursor-pointer rounded-lg border border-white/[0.08] px-4 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 transition hover:bg-white/[0.05] hover:text-white"><span class="label">{{ __('View All Sources') }}</span></button></div>
                    @endif
                </section>
            @endif

            <section id="deposits-history-wrapper" class="relative overflow-hidden rounded-2xl border border-white/[0.08] bg-[#111318]">
                <div id="deposits-loading-spinner" class="absolute inset-0 z-50 hidden flex-col items-center justify-center bg-[#0b0e11]/80 backdrop-blur-sm" aria-live="polite">
                    <div class="h-7 w-7 animate-spin rounded-full border-2 border-white/10 border-t-amber-400"></div><p class="mt-2 text-[10px] font-bold text-slate-400">{{ __('Loading deposits...') }}</p>
                </div>

                <div class="border-b border-white/[0.07] p-4 sm:p-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <div class="flex items-center gap-2"><h2 class="text-sm font-black text-white">{{ __('Deposit Records') }}</h2><span class="rounded-full border border-white/[0.08] bg-white/[0.03] px-2 py-0.5 text-[9px] font-bold text-slate-500">{{ $deposits->total() }}</span></div>
                            <p class="mt-1 text-[10px] text-slate-500">{{ __('Search references or move between status views.') }}</p>
                        </div>

                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <nav class="flex max-w-full overflow-x-auto rounded-lg bg-black/25 p-1" aria-label="{{ __('Deposit status') }}">
                                <a href="{{ route('user.deposits.index') }}" class="shrink-0 rounded-md bg-white/[0.08] px-3 py-1.5 text-[10px] font-bold text-white">{{ __('All') }}</a>
                                <a href="{{ route('user.deposits.pending') }}" class="shrink-0 rounded-md px-3 py-1.5 text-[10px] font-bold text-slate-500 transition hover:text-white">{{ __('Pending') }}</a>
                                <a href="{{ route('user.deposits.approved') }}" class="shrink-0 rounded-md px-3 py-1.5 text-[10px] font-bold text-slate-500 transition hover:text-white">{{ __('Completed') }}</a>
                                <a href="{{ route('user.deposits.failed') }}" class="shrink-0 rounded-md px-3 py-1.5 text-[10px] font-bold text-slate-500 transition hover:text-white">{{ __('Failed') }}</a>
                            </nav>

                            <form method="GET" action="{{ route('user.deposits.index') }}" class="flex min-w-0 gap-2">
                                <label for="deposit-search" class="sr-only">{{ __('Search deposits') }}</label>
                                <div class="relative min-w-0 flex-1 sm:w-56"><svg class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M20 20l-3.5-3.5"/></svg><input id="deposit-search" type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Reference or hash') }}" class="h-9 w-full rounded-lg border border-white/[0.09] bg-[#0b0e11] pl-9 pr-3 text-xs text-white outline-none placeholder:text-slate-600 focus:border-amber-400/60"></div>
                                <button type="submit" class="h-9 cursor-pointer rounded-lg bg-amber-400 px-3 text-[9px] font-black uppercase tracking-wider text-black">{{ __('Search') }}</button>
                                @if (request()->filled('search'))<a href="{{ route('user.deposits.index') }}" class="flex h-9 items-center rounded-lg border border-white/[0.08] px-3 text-[9px] font-bold uppercase text-slate-400">{{ __('Clear') }}</a>@endif
                            </form>
                        </div>
                    </div>
                </div>

                <div id="deposits-table-content">
                    <div class="hidden overflow-x-auto md:block">
                        <table class="w-full min-w-[820px] text-left">
                            <thead class="border-b border-white/[0.05] bg-black/10 text-[9px] font-bold uppercase tracking-[0.15em] text-slate-600"><tr><th class="px-5 py-3">{{ __('Deposit') }}</th><th class="px-4 py-3">{{ __('Amount') }}</th><th class="px-4 py-3">{{ __('Source') }}</th><th class="px-4 py-3">{{ __('Date') }}</th><th class="px-4 py-3">{{ __('Status') }}</th><th class="px-5 py-3 text-right">{{ __('Action') }}</th></tr></thead>
                            <tbody class="divide-y divide-white/[0.05]">
                                @forelse ($deposits as $deposit)
                                    @php
                                        $statusData = match ($deposit->status) {
                                            'pending' => [__('Pending'), 'border-amber-400/15 bg-amber-400/[0.07] text-amber-400'],
                                            'completed' => [__('Completed'), 'border-emerald-400/15 bg-emerald-400/[0.07] text-emerald-400'],
                                            'failed' => [__('Failed'), 'border-rose-400/15 bg-rose-400/[0.07] text-rose-400'],
                                            'partial_payment' => [__('Partial'), 'border-orange-400/15 bg-orange-400/[0.07] text-orange-400'],
                                            default => [__(str_replace('_', ' ', $deposit->status)), 'border-white/[0.08] bg-white/[0.03] text-slate-400'],
                                        };
                                        $shortReference = strlen($deposit->transaction_reference) > 14 ? substr($deposit->transaction_reference, 0, 7) . '…' . substr($deposit->transaction_reference, -5) : $deposit->transaction_reference;
                                    @endphp
                                    <tr class="transition hover:bg-white/[0.025]">
                                        <td class="px-5 py-3.5"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-400/[0.07] text-emerald-400">↓</span><div><a href="{{ route('user.deposits.view', $deposit->transaction_reference) }}" class="font-mono text-xs font-bold text-slate-200 transition hover:text-amber-400">{{ $shortReference }}</a>@if ($deposit->transaction_hash)<p class="mt-0.5 max-w-40 truncate font-mono text-[9px] text-slate-600" title="{{ $deposit->transaction_hash }}">{{ $deposit->transaction_hash }}</p>@endif</div></div></td>
                                        <td class="px-4 py-3.5"><div class="font-mono text-xs font-bold text-white">{{ number_format($deposit->amount, $decimalPlaces) }} {{ $currency }}</div>@if ($deposit->currency && $deposit->currency !== $currency)<div class="mt-0.5 font-mono text-[9px] text-slate-600">≈ {{ number_format($deposit->converted_amount, 6) }} {{ $deposit->currency }}</div>@endif</td>
                                        <td class="px-4 py-3.5 text-xs font-semibold text-slate-300">{{ $deposit->gatewayName() }}</td>
                                        <td class="px-4 py-3.5 text-[10px] text-slate-500"><span class="block text-slate-300">{{ $deposit->created_at->format('M d, Y') }}</span>{{ $deposit->created_at->format('H:i') }}</td>
                                        <td class="px-4 py-3.5"><span class="inline-flex rounded-full border px-2.5 py-1 text-[9px] font-bold uppercase tracking-wider {{ $statusData[1] }}">{{ $statusData[0] }}</span></td>
                                        <td class="px-5 py-3.5 text-right"><a href="{{ route('user.deposits.view', $deposit->transaction_reference) }}" aria-label="{{ __('View deposit :reference', ['reference' => $deposit->transaction_reference]) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-white/[0.08] text-slate-500 transition hover:bg-white/[0.05] hover:text-white"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-5 py-14 text-center"><div class="text-sm font-semibold text-slate-400">{{ __('No deposits found') }}</div><p class="mt-1 text-[10px] text-slate-600">{{ request()->filled('search') ? __('Try a different reference or clear your search.') : __('Your deposit activity will appear here.') }}</p>@unless(request()->filled('search'))<a href="{{ route('user.deposits.new') }}" class="mt-4 inline-flex rounded-lg bg-amber-400 px-4 py-2 text-[10px] font-black uppercase text-black">{{ __('Make a Deposit') }}</a>@endunless</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="divide-y divide-white/[0.06] md:hidden">
                        @forelse ($deposits as $deposit)
                            @php
                                $statusData = match ($deposit->status) {
                                    'pending' => [__('Pending'), 'text-amber-400'], 'completed' => [__('Completed'), 'text-emerald-400'], 'failed' => [__('Failed'), 'text-rose-400'], 'partial_payment' => [__('Partial'), 'text-orange-400'], default => [__(str_replace('_', ' ', $deposit->status)), 'text-slate-400'],
                                };
                            @endphp
                            <a href="{{ route('user.deposits.view', $deposit->transaction_reference) }}" class="block p-4 transition hover:bg-white/[0.025]">
                                <div class="flex items-start justify-between gap-4"><div class="min-w-0"><div class="flex items-center gap-2"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-400/[0.07] text-emerald-400">↓</span><div class="min-w-0"><p class="truncate text-xs font-bold text-slate-200">{{ $deposit->gatewayName() }}</p><p class="mt-0.5 truncate font-mono text-[9px] text-slate-600">{{ $deposit->transaction_reference }}</p></div></div></div><div class="shrink-0 text-right"><p class="font-mono text-xs font-bold text-white">{{ number_format($deposit->amount, $decimalPlaces) }} {{ $currency }}</p><p class="mt-1 text-[9px] font-bold uppercase {{ $statusData[1] }}">{{ $statusData[0] }}</p></div></div>
                                <div class="mt-3 flex items-center justify-between border-t border-white/[0.05] pt-2 text-[9px] text-slate-600">@if ($deposit->currency && $deposit->currency !== $currency)<span>≈ {{ number_format($deposit->converted_amount, 6) }} {{ $deposit->currency }}</span>@else<span>{{ __('Deposit') }}</span>@endif<time datetime="{{ $deposit->created_at->toIso8601String() }}">{{ $deposit->created_at->format('M d, Y · H:i') }}</time></div>
                            </a>
                        @empty
                            <div class="p-12 text-center"><div class="text-sm font-semibold text-slate-400">{{ __('No deposits found') }}</div><p class="mt-1 text-[10px] text-slate-600">{{ request()->filled('search') ? __('Try a different reference or clear your search.') : __('Your deposit activity will appear here.') }}</p></div>
                        @endforelse
                    </div>

                    @if ($deposits->hasPages())
                        <div class="ajax-pagination border-t border-white/[0.07] p-4 sm:p-5">{{ $deposits->withQueryString()->links('templates.york.blades.partials.pagination') }}</div>
                    @endif
                </div>
            </section>

            @if ($avg_processing_time || $highest_daily_total)
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @if ($avg_processing_time)<div class="rounded-xl border border-white/[0.07] bg-[#111318] p-4"><p class="text-[9px] font-bold uppercase tracking-wider text-slate-500">{{ __('Average Confirmation Time') }}</p><p class="mt-2 text-sm font-bold text-white">~{{ max(1, round($avg_processing_time / 60)) }} {{ __('minutes') }}</p><p class="mt-1 text-[10px] text-slate-600">{{ __('Based on completed deposits') }}</p></div>@endif
                    @if ($highest_daily_total)<div class="rounded-xl border border-white/[0.07] bg-[#111318] p-4"><p class="text-[9px] font-bold uppercase tracking-wider text-slate-500">{{ __('Peak Daily Volume') }}</p><p class="mt-2 font-mono text-sm font-bold text-emerald-400">{{ number_format($highest_daily_total->total, $decimalPlaces) }} {{ $currency }}</p><p class="mt-1 text-[10px] text-slate-600">{{ __('Highest recorded deposit day') }}</p></div>@endif
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const historyWrapper = document.getElementById('deposits-history-wrapper');
            const query = new URLSearchParams(window.location.search);
            if ((query.has('page') || query.has('search')) && historyWrapper) historyWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });

            $(document).on('click', '.ajax-pagination a', function(event) {
                event.preventDefault();
                const url = $(this).attr('href');
                const spinner = $('#deposits-loading-spinner');
                spinner.removeClass('hidden').addClass('flex');

                $.get(url).done(function(data) {
                    const content = $(data).find('#deposits-table-content').html();
                    if (content) {
                        $('#deposits-table-content').html(content);
                        window.history.replaceState({}, '', url);
                    }
                }).fail(function() {
                    if (typeof toastNotification === 'function') toastNotification({{ Illuminate\Support\Js::from(__('Could not load deposits. Please try again.')) }}, 'error');
                }).always(function() {
                    spinner.addClass('hidden').removeClass('flex');
                });
            });

            $(document).on('click', '.toggle-load-more', function() {
                const button = $(this);
                const container = $(button.data('target'));
                const expanded = button.attr('aria-expanded') === 'true';
                if (expanded) {
                    container.children().each(function(index) { $(this).toggleClass('hidden', index > 5); });
                } else {
                    container.children().removeClass('hidden');
                }
                button.attr('aria-expanded', expanded ? 'false' : 'true');
                button.find('.label').text(expanded ? {{ Illuminate\Support\Js::from(__('View All Sources')) }} : {{ Illuminate\Support\Js::from(__('Show Fewer Sources')) }});
            });
        });
    </script>
@endsection
