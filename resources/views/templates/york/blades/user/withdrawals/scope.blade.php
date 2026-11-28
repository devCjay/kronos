@extends('templates.york.blades.layouts.user')

@section('content')
    @php
        $currency = getSetting('currency');
        $decimalPlaces = (int) getSetting('decimal_places', 2);
        $scopeStyles = match ($scopeName) {
            'approved' => ['text-emerald-400', 'bg-emerald-400', __('Completed')],
            'failed' => ['text-rose-400', 'bg-rose-400', __('Failed')],
            'partial' => ['text-orange-400', 'bg-orange-400', __('Partial')],
            default => ['text-amber-400', 'bg-amber-400', __('Pending')],
        };
        $hasFilters = request()->filled('search') || request()->filled('from_date') || request()->filled('to_date') || request()->filled('blockchain_id') || request()->filled('sort') || request()->filled('direction');
    @endphp

    <div class="relative min-h-screen pb-12">
        <div class="pointer-events-none fixed right-0 top-0 -z-0 h-[38rem] w-[38rem] -translate-y-1/3 translate-x-1/3 rounded-full bg-amber-500/[0.04] blur-[140px]"></div>

        <div class="relative z-10 mx-auto max-w-7xl space-y-4">
            <header class="rounded-2xl border border-white/[0.08] bg-[#111318] p-5 sm:p-6">
                <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                    <div>
                        <div class="mb-2 flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.2em] {{ $scopeStyles[0] }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $scopeStyles[1] }}"></span>
                            {{ __('Withdrawal status') }}
                        </div>
                        <h1 class="font-heading text-2xl font-black tracking-tight text-white sm:text-3xl">{{ $page_title }}</h1>
                        <p class="mt-1.5 max-w-2xl text-xs leading-5 text-slate-400">{{ __('Review :status payout records, destination networks, values, and transaction references.', ['status' => strtolower($scopeStyles[2])]) }}</p>
                    </div>
                    <a href="{{ route('user.withdrawals.new') }}" class="flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-amber-400 px-5 text-xs font-black uppercase tracking-wider text-black transition hover:bg-amber-300 focus:outline-none focus:ring-4 focus:ring-amber-400/20 sm:w-auto">
                        <span class="text-base leading-none">+</span>{{ __('New Withdrawal') }}
                    </a>
                </div>
            </header>

            <section class="grid grid-cols-1 gap-3 sm:grid-cols-2" aria-label="{{ __('Filtered withdrawal summary') }}">
                <article class="rounded-xl border border-white/[0.07] bg-[#111318] p-4">
                    <p class="text-[9px] font-bold uppercase tracking-[0.15em] text-slate-500">{{ $scopeStyles[2] }} {{ __('Volume') }}</p>
                    <p class="mt-2 break-all font-mono text-xl font-black {{ $scopeStyles[0] }}">{{ number_format((float) $totalAmount, $decimalPlaces) }} <span class="text-[9px] font-semibold text-slate-600">{{ $currency }}</span></p>
                    <p class="mt-1 text-[10px] text-slate-600">{{ __('Current filter total') }}</p>
                </article>
                <article class="rounded-xl border border-white/[0.07] bg-[#111318] p-4">
                    <p class="text-[9px] font-bold uppercase tracking-[0.15em] text-slate-500">{{ __('Matching Records') }}</p>
                    <p class="mt-2 font-mono text-xl font-black text-white">{{ $withdrawals->total() }}</p>
                    <p class="mt-1 text-[10px] text-slate-600">{{ $hasFilters ? __('After applying filters') : __('All records in this status') }}</p>
                </article>
            </section>

            <section class="overflow-hidden rounded-2xl border border-white/[0.08] bg-[#111318]">
                <div class="border-b border-white/[0.07] p-4 sm:p-5">
                    <nav class="flex max-w-full overflow-x-auto rounded-lg bg-black/25 p-1" aria-label="{{ __('Withdrawal status') }}">
                        @foreach ([
                            ['user.withdrawals.index', __('All')],
                            ['user.withdrawals.pending', __('Pending')],
                            ['user.withdrawals.approved', __('Completed')],
                            ['user.withdrawals.failed', __('Failed')],
                            ['user.withdrawals.partial', __('Partial')],
                        ] as [$routeName, $label])
                            <a href="{{ route($routeName) }}" class="shrink-0 rounded-md px-3 py-1.5 text-[10px] font-bold transition {{ request()->routeIs($routeName) ? 'bg-white/[0.08] text-white' : 'text-slate-500 hover:text-white' }}">{{ $label }}</a>
                        @endforeach
                    </nav>

                    <form action="{{ url()->current() }}" method="GET" class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2 xl:grid-cols-6">
                        <div class="relative sm:col-span-2 xl:col-span-2">
                            <label for="scope-search" class="sr-only">{{ __('Search withdrawals') }}</label>
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M20 20l-3.5-3.5"/></svg>
                            <input id="scope-search" type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Reference, hash, or amount') }}" class="h-10 w-full rounded-lg border border-white/[0.09] bg-[#0b0e11] pl-9 pr-3 text-xs text-white outline-none placeholder:text-slate-600 focus:border-amber-400/60">
                        </div>

                        <div>
                            <label for="scope-network" class="sr-only">{{ __('Network') }}</label>
                            <select id="scope-network" name="blockchain_id" class="h-10 w-full rounded-lg border border-white/[0.09] bg-[#0b0e11] px-3 text-xs text-slate-300 outline-none focus:border-amber-400/60">
                                <option value="">{{ __('All networks') }}</option>
                                @foreach ($blockchains as $blockchain)
                                    <option value="{{ $blockchain->id }}" @selected((string) request('blockchain_id') === (string) $blockchain->id)>{{ $blockchain->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="scope-from" class="sr-only">{{ __('From date') }}</label>
                            <input id="scope-from" type="date" name="from_date" value="{{ request('from_date') }}" class="h-10 w-full rounded-lg border border-white/[0.09] bg-[#0b0e11] px-3 text-xs text-slate-300 outline-none focus:border-amber-400/60">
                        </div>
                        <div>
                            <label for="scope-to" class="sr-only">{{ __('To date') }}</label>
                            <input id="scope-to" type="date" name="to_date" value="{{ request('to_date') }}" class="h-10 w-full rounded-lg border border-white/[0.09] bg-[#0b0e11] px-3 text-xs text-slate-300 outline-none focus:border-amber-400/60">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="h-10 flex-1 cursor-pointer rounded-lg bg-amber-400 px-3 text-[9px] font-black uppercase tracking-wider text-black">{{ __('Filter') }}</button>
                            @if ($hasFilters)<a href="{{ url()->current() }}" class="flex h-10 items-center rounded-lg border border-white/[0.08] px-3 text-[9px] font-bold uppercase text-slate-400">{{ __('Clear') }}</a>@endif
                        </div>

                        <div class="flex gap-2 sm:col-span-2 xl:col-span-6">
                            <label for="scope-sort" class="flex items-center text-[9px] font-bold uppercase tracking-wider text-slate-600">{{ __('Sort') }}</label>
                            <select id="scope-sort" name="sort" class="h-8 rounded-lg border border-white/[0.08] bg-[#0b0e11] px-2 text-[10px] text-slate-400 outline-none">
                                <option value="created_at" @selected(request('sort', 'created_at') === 'created_at')>{{ __('Date') }}</option>
                                <option value="amount" @selected(request('sort') === 'amount')>{{ __('Amount') }}</option>
                                <option value="amount_payable" @selected(request('sort') === 'amount_payable')>{{ __('Payable amount') }}</option>
                                <option value="transaction_reference" @selected(request('sort') === 'transaction_reference')>{{ __('Reference') }}</option>
                            </select>
                            <select name="direction" class="h-8 rounded-lg border border-white/[0.08] bg-[#0b0e11] px-2 text-[10px] text-slate-400 outline-none">
                                <option value="desc" @selected(request('direction', 'desc') === 'desc')>{{ __('Newest / highest') }}</option>
                                <option value="asc" @selected(request('direction') === 'asc')>{{ __('Oldest / lowest') }}</option>
                            </select>
                        </div>
                    </form>
                </div>

                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full min-w-[860px] text-left">
                        <thead class="border-b border-white/[0.05] bg-black/10 text-[9px] font-bold uppercase tracking-[0.15em] text-slate-600"><tr><th class="px-5 py-3">{{ __('Withdrawal') }}</th><th class="px-4 py-3">{{ __('Amount') }}</th><th class="px-4 py-3">{{ __('Destination') }}</th><th class="px-4 py-3">{{ __('Date') }}</th><th class="px-4 py-3">{{ __('Status') }}</th><th class="px-5 py-3 text-right">{{ __('Action') }}</th></tr></thead>
                        <tbody class="divide-y divide-white/[0.05]">
                            @forelse ($withdrawals as $withdrawal)
                                @php $shortReference = strlen($withdrawal->transaction_reference) > 14 ? substr($withdrawal->transaction_reference, 0, 7) . '...' . substr($withdrawal->transaction_reference, -5) : $withdrawal->transaction_reference; @endphp
                                <tr class="transition hover:bg-white/[0.025]">
                                    <td class="px-5 py-3.5"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-400/[0.07] text-amber-400" aria-hidden="true">&uarr;</span><div><a href="{{ route('user.withdrawals.view', $withdrawal->transaction_reference) }}" class="font-mono text-xs font-bold text-slate-200 transition hover:text-amber-400">{{ $shortReference }}</a>@if ($withdrawal->transaction_hash)<p class="mt-0.5 max-w-40 truncate font-mono text-[9px] text-slate-600" title="{{ $withdrawal->transaction_hash }}">{{ $withdrawal->transaction_hash }}</p>@endif</div></div></td>
                                    <td class="px-4 py-3.5"><div class="font-mono text-xs font-bold text-white">{{ number_format($withdrawal->amount, $decimalPlaces) }} {{ $currency }}</div>@if ($withdrawal->currency && $withdrawal->currency !== $currency)<div class="mt-0.5 font-mono text-[9px] text-slate-600">{{ number_format($withdrawal->amount_payable, 6) }} {{ $withdrawal->currency }}</div>@endif</td>
                                    <td class="px-4 py-3.5 text-xs font-semibold text-slate-300">{{ $withdrawal->gatewayName() }}</td>
                                    <td class="px-4 py-3.5 text-[10px] text-slate-500"><span class="block text-slate-300">{{ $withdrawal->created_at->format('M d, Y') }}</span>{{ $withdrawal->created_at->format('H:i') }}</td>
                                    <td class="px-4 py-3.5"><span class="inline-flex rounded-full border border-white/[0.08] bg-white/[0.03] px-2.5 py-1 text-[9px] font-bold uppercase tracking-wider {{ $scopeStyles[0] }}">{{ $scopeStyles[2] }}</span></td>
                                    <td class="px-5 py-3.5 text-right"><a href="{{ route('user.withdrawals.view', $withdrawal->transaction_reference) }}" aria-label="{{ __('View withdrawal :reference', ['reference' => $withdrawal->transaction_reference]) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-white/[0.08] text-slate-500 transition hover:bg-white/[0.05] hover:text-white"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-14 text-center"><div class="text-sm font-semibold text-slate-400">{{ __('No :status withdrawals found', ['status' => strtolower($scopeStyles[2])]) }}</div><p class="mt-1 text-[10px] text-slate-600">{{ $hasFilters ? __('Adjust or clear the current filters.') : __('Matching withdrawal activity will appear here.') }}</p></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="divide-y divide-white/[0.06] md:hidden">
                    @forelse ($withdrawals as $withdrawal)
                        <a href="{{ route('user.withdrawals.view', $withdrawal->transaction_reference) }}" class="block p-4 transition hover:bg-white/[0.025]">
                            <div class="flex items-start justify-between gap-4"><div class="min-w-0"><div class="flex items-center gap-2"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-400/[0.07] text-amber-400" aria-hidden="true">&uarr;</span><div class="min-w-0"><p class="truncate text-xs font-bold text-slate-200">{{ $withdrawal->gatewayName() }}</p><p class="mt-0.5 truncate font-mono text-[9px] text-slate-600">{{ $withdrawal->transaction_reference }}</p></div></div></div><div class="shrink-0 text-right"><p class="font-mono text-xs font-bold text-white">{{ number_format($withdrawal->amount, $decimalPlaces) }} {{ $currency }}</p><p class="mt-1 text-[9px] font-bold uppercase {{ $scopeStyles[0] }}">{{ $scopeStyles[2] }}</p></div></div>
                            <div class="mt-3 flex items-center justify-between border-t border-white/[0.05] pt-2 text-[9px] text-slate-600">@if ($withdrawal->currency && $withdrawal->currency !== $currency)<span>{{ number_format($withdrawal->amount_payable, 6) }} {{ $withdrawal->currency }}</span>@else<span>{{ __('Withdrawal') }}</span>@endif<time datetime="{{ $withdrawal->created_at->toIso8601String() }}">{{ $withdrawal->created_at->format('M d, Y - H:i') }}</time></div>
                        </a>
                    @empty
                        <div class="p-12 text-center"><div class="text-sm font-semibold text-slate-400">{{ __('No :status withdrawals found', ['status' => strtolower($scopeStyles[2])]) }}</div><p class="mt-1 text-[10px] text-slate-600">{{ $hasFilters ? __('Adjust or clear the current filters.') : __('Matching withdrawal activity will appear here.') }}</p></div>
                    @endforelse
                </div>

                @if ($withdrawals->hasPages())
                    <div class="border-t border-white/[0.07] p-4 sm:p-5">{{ $withdrawals->withQueryString()->links('templates.york.blades.partials.pagination') }}</div>
                @endif
            </section>
        </div>
    </div>
@endsection
