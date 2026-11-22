@extends('templates.' . config('site.template') . '.blades.layouts.user')

@section('content')
    @php
        $decimalPlaces = (int) getSetting('decimal_places', 2);
        $currency = getSetting('currency');
        $totalCredits = (float) ($moneyFlow->total_credits ?? 0);
        $totalDebits = (float) ($moneyFlow->total_debits ?? 0);
        $hasFilters = request()->filled('search') || (request('type', 'all') !== 'all') || (request('status', 'all') !== 'all') || request()->filled('date_from') || request()->filled('date_to');
    @endphp

    <div class="relative min-h-screen pb-12">
        <div class="pointer-events-none fixed right-0 top-0 -z-0 h-[38rem] w-[38rem] -translate-y-1/3 translate-x-1/3 rounded-full bg-amber-500/[0.04] blur-[140px]"></div>

        <div class="relative z-10 mx-auto max-w-7xl space-y-4">
            <header class="rounded-2xl border border-white/[0.08] bg-[#111318] p-5 sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <div class="mb-2 flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.2em] text-amber-400">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-400 shadow-[0_0_8px_rgba(247,166,0,.75)]"></span>
                            {{ __('Account ledger') }}
                        </div>
                        <h1 class="font-heading text-2xl font-black tracking-tight text-white sm:text-3xl">{{ __('Transaction History') }}</h1>
                        <p class="mt-1.5 max-w-2xl text-xs leading-5 text-slate-400">{{ __('Review completed and pending account entries, search references, and export your filtered statement.') }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 sm:flex">
                        <a href="{{ route('user.transactions', array_merge(request()->except(['page', 'export']), ['export' => 'csv'])) }}"
                            class="flex min-h-10 items-center justify-center gap-2 rounded-xl border border-white/[0.09] bg-white/[0.035] px-4 text-[10px] font-bold uppercase tracking-wider text-slate-200 transition hover:bg-white/[0.07] focus:outline-none focus:ring-2 focus:ring-amber-400/30">
                            <svg class="h-3.5 w-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-4-4m4 4l4-4M5 20h14"/></svg>
                            {{ __('Export CSV') }}
                        </a>
                        <a href="{{ route('user.transactions', array_merge(request()->except(['page', 'export']), ['export' => 'pdf'])) }}"
                            class="flex min-h-10 items-center justify-center gap-2 rounded-xl bg-amber-400 px-4 text-[10px] font-black uppercase tracking-wider text-black transition hover:bg-amber-300 focus:outline-none focus:ring-4 focus:ring-amber-400/20">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3h7l5 5v13H7V3zm7 0v6h6"/></svg>
                            {{ __('Export PDF') }}
                        </a>
                    </div>
                </div>
            </header>

            <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="{{ __('Transaction summary') }}">
                <article class="rounded-xl border border-white/[0.07] bg-[#111318] p-4 sm:col-span-2 xl:col-span-1">
                    <p class="text-[9px] font-bold uppercase tracking-[0.15em] text-slate-500">{{ __('Current Balance') }}</p>
                    <p class="mt-2 break-all font-mono text-xl font-black text-white">{{ number_format($currentBalance, $decimalPlaces) }} <span class="text-[10px] font-semibold text-slate-500">{{ $currency }}</span></p>
                    <p class="mt-1 text-[10px] text-slate-600">{{ __('Available account balance') }}</p>
                </article>
                <article class="rounded-xl border border-white/[0.07] bg-[#111318] p-4">
                    <p class="text-[9px] font-bold uppercase tracking-[0.15em] text-slate-500">{{ __('Completed Credits') }}</p>
                    <p class="mt-2 break-all font-mono text-xl font-black text-emerald-400">+{{ number_format($totalCredits, $decimalPlaces) }}</p>
                    <p class="mt-1 text-[10px] text-slate-600">{{ __('All-time completed entries') }}</p>
                </article>
                <article class="rounded-xl border border-white/[0.07] bg-[#111318] p-4">
                    <p class="text-[9px] font-bold uppercase tracking-[0.15em] text-slate-500">{{ __('Completed Debits') }}</p>
                    <p class="mt-2 break-all font-mono text-xl font-black text-rose-400">-{{ number_format($totalDebits, $decimalPlaces) }}</p>
                    <p class="mt-1 text-[10px] text-slate-600">{{ __('All-time completed entries') }}</p>
                </article>
                <article class="rounded-xl border border-white/[0.07] bg-[#111318] p-4">
                    <p class="text-[9px] font-bold uppercase tracking-[0.15em] text-slate-500">{{ __('Net Completed Flow') }}</p>
                    <p class="mt-2 break-all font-mono text-xl font-black {{ $netFlow >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">{{ $netFlow >= 0 ? '+' : '' }}{{ number_format($netFlow, $decimalPlaces) }}</p>
                    <p class="mt-1 text-[10px] text-slate-600">{{ __('Credits minus debits') }}</p>
                </article>
            </section>

            <section class="overflow-hidden rounded-2xl border border-white/[0.08] bg-[#111318]" id="transactions-wrapper">
                <div class="border-b border-white/[0.07] p-4 sm:p-5">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                        <div class="shrink-0">
                            <h2 class="text-sm font-black text-white">{{ __('All Transactions') }}</h2>
                            <p class="mt-1 text-[10px] text-slate-500">{{ __(':count entries on this page', ['count' => $transactions->count()]) }}</p>
                        </div>

                        <form id="filter-form" action="{{ route('user.transactions') }}" method="GET" class="grid grid-cols-1 gap-2 sm:grid-cols-2 xl:flex xl:items-end">
                            <div class="sm:col-span-2 xl:w-60">
                                <label for="transaction-search" class="sr-only">{{ __('Search transactions') }}</label>
                                <div class="relative">
                                    <svg class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M20 20l-3.5-3.5"/></svg>
                                    <input id="transaction-search" type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Reference or description') }}"
                                        class="h-10 w-full rounded-lg border border-white/[0.09] bg-[#0b0e11] pl-9 pr-3 text-xs text-white outline-none transition placeholder:text-slate-600 focus:border-amber-400/60 focus:ring-2 focus:ring-amber-400/[0.08]">
                                </div>
                            </div>

                            <div>
                                <label for="transaction-type" class="sr-only">{{ __('Transaction type') }}</label>
                                <select id="transaction-type" name="type" class="h-10 w-full rounded-lg border border-white/[0.09] bg-[#0b0e11] px-3 text-xs text-slate-300 outline-none focus:border-amber-400/60">
                                    <option value="all" @selected(request('type', 'all') === 'all')>{{ __('All Types') }}</option>
                                    <option value="credit" @selected(request('type') === 'credit')>{{ __('Credit') }}</option>
                                    <option value="debit" @selected(request('type') === 'debit')>{{ __('Debit') }}</option>
                                </select>
                            </div>

                            <div>
                                <label for="transaction-status" class="sr-only">{{ __('Transaction status') }}</label>
                                <select id="transaction-status" name="status" class="h-10 w-full rounded-lg border border-white/[0.09] bg-[#0b0e11] px-3 text-xs text-slate-300 outline-none focus:border-amber-400/60">
                                    <option value="all" @selected(request('status', 'all') === 'all')>{{ __('All Statuses') }}</option>
                                    <option value="completed" @selected(request('status') === 'completed')>{{ __('Completed') }}</option>
                                    <option value="pending" @selected(request('status') === 'pending')>{{ __('Pending') }}</option>
                                    <option value="failed" @selected(request('status') === 'failed')>{{ __('Failed') }}</option>
                                    <option value="rejected" @selected(request('status') === 'rejected')>{{ __('Rejected') }}</option>
                                </select>
                            </div>

                            <div><label for="date-from" class="sr-only">{{ __('From date') }}</label><input id="date-from" type="date" name="date_from" value="{{ request('date_from') }}" max="{{ now()->format('Y-m-d') }}" class="h-10 w-full rounded-lg border border-white/[0.09] bg-[#0b0e11] px-3 text-xs text-slate-300 outline-none focus:border-amber-400/60"></div>
                            <div><label for="date-to" class="sr-only">{{ __('To date') }}</label><input id="date-to" type="date" name="date_to" value="{{ request('date_to') }}" max="{{ now()->format('Y-m-d') }}" class="h-10 w-full rounded-lg border border-white/[0.09] bg-[#0b0e11] px-3 text-xs text-slate-300 outline-none focus:border-amber-400/60"></div>

                            <button type="submit" class="h-10 cursor-pointer rounded-lg bg-amber-400 px-4 text-[10px] font-black uppercase tracking-wider text-black transition hover:bg-amber-300 focus:outline-none focus:ring-4 focus:ring-amber-400/20">{{ __('Apply') }}</button>
                            @if ($hasFilters)
                                <a href="{{ route('user.transactions') }}" class="flex h-10 items-center justify-center rounded-lg border border-white/[0.09] px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 transition hover:bg-white/[0.05] hover:text-white">{{ __('Clear') }}</a>
                            @endif
                        </form>
                    </div>
                </div>

                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full min-w-[760px] text-left">
                        <thead class="border-b border-white/[0.05] bg-black/10 text-[9px] font-bold uppercase tracking-[0.15em] text-slate-600">
                            <tr><th class="px-5 py-3">{{ __('Transaction') }}</th><th class="px-4 py-3">{{ __('Reference') }}</th><th class="px-4 py-3">{{ __('Date') }}</th><th class="px-4 py-3">{{ __('Status') }}</th><th class="px-5 py-3 text-right">{{ __('Amount') }}</th></tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.05]">
                            @forelse ($transactions as $tx)
                                @php
                                    $isCredit = strtolower($tx->type ?? '') === 'credit' || ($tx->tx_type ?? null) === '+';
                                    $status = strtolower($tx->status ?? 'completed');
                                    $reference = $tx->tnx_id ?? $tx->transaction_id ?? $tx->reference ?? '#' . $tx->id;
                                    $statusClass = match ($status) {
                                        'completed', 'approved' => 'border-emerald-400/15 bg-emerald-400/[0.07] text-emerald-400',
                                        'pending', 'processing' => 'border-amber-400/15 bg-amber-400/[0.07] text-amber-400',
                                        'failed', 'rejected', 'cancelled' => 'border-rose-400/15 bg-rose-400/[0.07] text-rose-400',
                                        default => 'border-white/[0.08] bg-white/[0.035] text-slate-400',
                                    };
                                @endphp
                                <tr class="transition hover:bg-white/[0.025]">
                                    <td class="px-5 py-3.5"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $isCredit ? 'bg-emerald-400/[0.08] text-emerald-400' : 'bg-rose-400/[0.08] text-rose-400' }}">{{ $isCredit ? '↓' : '↑' }}</span><div class="min-w-0"><div class="max-w-[260px] truncate text-xs font-semibold text-slate-200">{{ $tx->description ?? $tx->remark ?? __('Transaction') }}</div><div class="mt-0.5 text-[9px] uppercase tracking-wider text-slate-600">{{ $isCredit ? __('Credit') : __('Debit') }}</div></div></div></td>
                                    <td class="px-4 py-3.5 font-mono text-[10px] text-slate-500">{{ $reference }}</td>
                                    <td class="px-4 py-3.5 text-[10px] text-slate-500"><span class="block text-slate-300">{{ $tx->created_at->format('M d, Y') }}</span>{{ $tx->created_at->format('H:i') }}</td>
                                    <td class="px-4 py-3.5"><span class="inline-flex rounded-full border px-2.5 py-1 text-[9px] font-bold uppercase tracking-wider {{ $statusClass }}">{{ __($status) }}</span></td>
                                    <td class="px-5 py-3.5 text-right font-mono text-xs font-bold {{ $isCredit ? 'text-emerald-400' : 'text-rose-400' }}">{{ $isCredit ? '+' : '-' }}{{ showAmount($tx->amount) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-5 py-14 text-center"><div class="text-sm font-semibold text-slate-400">{{ __('No transactions found') }}</div><p class="mt-1 text-[10px] text-slate-600">{{ $hasFilters ? __('Try changing or clearing your filters.') : __('Your account ledger will appear here.') }}</p></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="divide-y divide-white/[0.06] md:hidden">
                    @forelse ($transactions as $tx)
                        @php
                            $isCredit = strtolower($tx->type ?? '') === 'credit' || ($tx->tx_type ?? null) === '+';
                            $status = strtolower($tx->status ?? 'completed');
                            $reference = $tx->tnx_id ?? $tx->transaction_id ?? $tx->reference ?? '#' . $tx->id;
                            $statusClass = match ($status) {
                                'completed', 'approved' => 'text-emerald-400',
                                'pending', 'processing' => 'text-amber-400',
                                'failed', 'rejected', 'cancelled' => 'text-rose-400',
                                default => 'text-slate-400',
                            };
                        @endphp
                        <article class="p-4">
                            <div class="flex items-start justify-between gap-4"><div class="flex min-w-0 items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $isCredit ? 'bg-emerald-400/[0.08] text-emerald-400' : 'bg-rose-400/[0.08] text-rose-400' }}">{{ $isCredit ? '↓' : '↑' }}</span><div class="min-w-0"><h3 class="truncate text-xs font-semibold text-slate-200">{{ $tx->description ?? $tx->remark ?? __('Transaction') }}</h3><p class="mt-0.5 truncate font-mono text-[9px] text-slate-600">{{ $reference }}</p></div></div><div class="shrink-0 text-right"><div class="font-mono text-xs font-bold {{ $isCredit ? 'text-emerald-400' : 'text-rose-400' }}">{{ $isCredit ? '+' : '-' }}{{ showAmount($tx->amount) }}</div><div class="mt-1 text-[9px] font-bold uppercase {{ $statusClass }}">{{ __($status) }}</div></div></div>
                            <div class="mt-3 flex items-center justify-between border-t border-white/[0.05] pt-2 text-[9px] text-slate-600"><span>{{ $isCredit ? __('Credit') : __('Debit') }}</span><time datetime="{{ $tx->created_at->toIso8601String() }}">{{ $tx->created_at->format('M d, Y · H:i') }}</time></div>
                        </article>
                    @empty
                        <div class="p-12 text-center"><div class="text-sm font-semibold text-slate-400">{{ __('No transactions found') }}</div><p class="mt-1 text-[10px] text-slate-600">{{ $hasFilters ? __('Try changing or clearing your filters.') : __('Your account ledger will appear here.') }}</p></div>
                    @endforelse
                </div>

                @if ($transactions->hasPages())
                    <div class="border-t border-white/[0.07] px-4 py-4 sm:px-5">{{ $transactions->withQueryString()->links() }}</div>
                @endif
            </section>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-white/[0.07] bg-[#111318] p-4"><p class="text-[9px] font-bold uppercase tracking-wider text-slate-500">{{ __('Activity') }}</p><p class="mt-2 text-sm font-bold text-white">{{ $activity->month_count }} {{ __('this month') }}</p><p class="mt-1 text-[10px] text-slate-600">{{ $activity->today_count }} {{ __('today') }} · {{ $activity->week_count }} {{ __('this week') }}</p></div>
                <div class="rounded-xl border border-white/[0.07] bg-[#111318] p-4"><p class="text-[9px] font-bold uppercase tracking-wider text-slate-500">{{ __('Status Overview') }}</p><p class="mt-2 text-sm font-bold text-white">{{ round($successRate) }}% {{ __('completed') }}</p><p class="mt-1 text-[10px] text-slate-600">{{ $statusCounts->pending_count }} {{ __('pending') }} · {{ $statusCounts->failed_count }} {{ __('failed') }}</p></div>
                <div class="rounded-xl border border-white/[0.07] bg-[#111318] p-4"><p class="text-[9px] font-bold uppercase tracking-wider text-slate-500">{{ __('Average Completed Entry') }}</p><p class="mt-2 text-sm font-bold text-emerald-400">{{ number_format((float) ($sizeStats->avg_credit ?? 0), $decimalPlaces) }} <span class="text-[9px] text-slate-600">{{ __('credit') }}</span></p><p class="mt-1 text-[10px] text-rose-400">{{ number_format((float) ($sizeStats->avg_debit ?? 0), $decimalPlaces) }} <span class="text-slate-600">{{ __('debit') }}</span></p></div>
            </div>
        </div>
    </div>
@endsection
