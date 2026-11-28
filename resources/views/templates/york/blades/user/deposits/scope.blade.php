@extends('templates.york.blades.layouts.user')

@section('content')
    @php
        $currency = getSetting('currency');
        $decimalPlaces = (int) getSetting('decimal_places', 2);
        $scopeData = match ($status) {
            'pending' => [__('Pending Deposits'), __('Deposits awaiting confirmation or review.'), 'text-amber-400', 'bg-amber-400', 'border-amber-400/15 bg-amber-400/[0.07] text-amber-400'],
            'failed' => [__('Failed Deposits'), __('Deposits that were not completed successfully.'), 'text-rose-400', 'bg-rose-400', 'border-rose-400/15 bg-rose-400/[0.07] text-rose-400'],
            default => [__('Completed Deposits'), __('Deposits confirmed and credited to your account.'), 'text-emerald-400', 'bg-emerald-400', 'border-emerald-400/15 bg-emerald-400/[0.07] text-emerald-400'],
        };
        $hasFilters = request()->filled('search') || request()->filled('from_date') || request()->filled('to_date') || request()->filled('method_id') || request()->filled('sort') || request()->filled('direction');
    @endphp

    <div class="relative min-h-screen pb-12">
        <div class="pointer-events-none fixed right-0 top-0 -z-0 h-[38rem] w-[38rem] -translate-y-1/3 translate-x-1/3 rounded-full bg-amber-500/[0.04] blur-[140px]"></div>

        <div class="relative z-10 mx-auto max-w-7xl space-y-4">
            <header class="rounded-2xl border border-white/[0.08] bg-[#111318] p-5 sm:p-6">
                <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                    <div>
                        <div class="mb-2 flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.2em] text-slate-500"><span class="h-1.5 w-1.5 rounded-full {{ $scopeData[3] }}"></span>{{ __('Deposit status') }}</div>
                        <h1 class="font-heading text-2xl font-black tracking-tight text-white sm:text-3xl">{{ $scopeData[0] }}</h1>
                        <p class="mt-1.5 max-w-2xl text-xs leading-5 text-slate-400">{{ $scopeData[1] }}</p>
                    </div>
                    <div class="flex items-end justify-between gap-5 rounded-xl border border-white/[0.07] bg-black/15 px-4 py-3 md:block md:min-w-52 md:text-right">
                        <div><p class="text-[9px] font-bold uppercase tracking-[0.14em] text-slate-600">{{ __('Filtered total') }}</p><p class="mt-1 font-mono text-lg font-black {{ $scopeData[2] }}">{{ number_format($totalAmount, $decimalPlaces) }} <span class="text-[9px] text-slate-600">{{ $currency }}</span></p></div>
                        <p class="text-[10px] text-slate-500 md:mt-1">{{ $deposits->total() }} {{ __('records') }}</p>
                    </div>
                </div>
            </header>

            <section id="deposits-history-wrapper" class="relative overflow-hidden rounded-2xl border border-white/[0.08] bg-[#111318]">
                <div id="deposits-loading-spinner" class="absolute inset-0 z-50 hidden flex-col items-center justify-center bg-[#0b0e11]/80 backdrop-blur-sm" aria-live="polite"><div class="h-7 w-7 animate-spin rounded-full border-2 border-white/10 border-t-amber-400"></div><p class="mt-2 text-[10px] font-bold text-slate-400">{{ __('Loading deposits...') }}</p></div>

                <div class="border-b border-white/[0.07] p-4 sm:p-5">
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <nav class="flex max-w-full overflow-x-auto rounded-lg bg-black/25 p-1" aria-label="{{ __('Deposit status') }}">
                                <a href="{{ route('user.deposits.index') }}" class="shrink-0 rounded-md px-3 py-1.5 text-[10px] font-bold text-slate-500 transition hover:text-white">{{ __('All') }}</a>
                                <a href="{{ route('user.deposits.pending') }}" class="shrink-0 rounded-md px-3 py-1.5 text-[10px] font-bold {{ $status === 'pending' ? 'bg-white/[0.08] text-white' : 'text-slate-500 hover:text-white' }}">{{ __('Pending') }}</a>
                                <a href="{{ route('user.deposits.approved') }}" class="shrink-0 rounded-md px-3 py-1.5 text-[10px] font-bold {{ $status === 'completed' ? 'bg-white/[0.08] text-white' : 'text-slate-500 hover:text-white' }}">{{ __('Completed') }}</a>
                                <a href="{{ route('user.deposits.failed') }}" class="shrink-0 rounded-md px-3 py-1.5 text-[10px] font-bold {{ $status === 'failed' ? 'bg-white/[0.08] text-white' : 'text-slate-500 hover:text-white' }}">{{ __('Failed') }}</a>
                            </nav>
                            <a href="{{ route('user.deposits.new') }}" class="flex min-h-9 items-center justify-center rounded-lg bg-amber-400 px-4 text-[9px] font-black uppercase tracking-wider text-black transition hover:bg-amber-300">{{ __('New Deposit') }}</a>
                        </div>

                        <form id="scope-filter-form" action="{{ url()->current() }}" method="GET" class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-6">
                            <div class="relative sm:col-span-2"><label for="scope-search" class="sr-only">{{ __('Search deposits') }}</label><svg class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M20 20l-3.5-3.5"/></svg><input id="scope-search" type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Reference, hash, or amount') }}" class="h-10 w-full rounded-lg border border-white/[0.09] bg-[#0b0e11] pl-9 pr-3 text-xs text-white outline-none placeholder:text-slate-600 focus:border-amber-400/60"></div>
                            <div><label for="from-date" class="sr-only">{{ __('From date') }}</label><input id="from-date" type="date" name="from_date" value="{{ request('from_date') }}" max="{{ now()->format('Y-m-d') }}" class="h-10 w-full rounded-lg border border-white/[0.09] bg-[#0b0e11] px-3 text-xs text-slate-300 outline-none focus:border-amber-400/60"></div>
                            <div><label for="to-date" class="sr-only">{{ __('To date') }}</label><input id="to-date" type="date" name="to_date" value="{{ request('to_date') }}" max="{{ now()->format('Y-m-d') }}" class="h-10 w-full rounded-lg border border-white/[0.09] bg-[#0b0e11] px-3 text-xs text-slate-300 outline-none focus:border-amber-400/60"></div>
                            <div><label for="deposit-method" class="sr-only">{{ __('Deposit source') }}</label><select id="deposit-method" name="method_id" class="h-10 w-full rounded-lg border border-white/[0.09] bg-[#0b0e11] px-3 text-xs text-slate-300 outline-none focus:border-amber-400/60"><option value="">{{ __('All Sources') }}</option>@foreach ($payment_methods as $method)<option value="{{ $method->id }}" @selected((string) request('method_id') === (string) $method->id)>{{ $method->name }}</option>@endforeach</select></div>
                            <div><label for="deposit-sort" class="sr-only">{{ __('Sort deposits') }}</label><select id="deposit-sort" name="sort" class="h-10 w-full rounded-lg border border-white/[0.09] bg-[#0b0e11] px-3 text-xs text-slate-300 outline-none focus:border-amber-400/60"><option value="created_at" @selected(request('sort', 'created_at') === 'created_at')>{{ __('Date') }}</option><option value="amount" @selected(request('sort') === 'amount')>{{ __('Amount') }}</option></select><input type="hidden" name="direction" value="{{ request('direction', 'desc') === 'asc' ? 'asc' : 'desc' }}"></div>
                            <div class="flex gap-2 sm:col-span-2 lg:col-span-6 lg:justify-end"><button type="submit" class="h-10 flex-1 cursor-pointer rounded-lg bg-amber-400 px-4 text-[9px] font-black uppercase tracking-wider text-black transition hover:bg-amber-300 sm:flex-none">{{ __('Apply Filters') }}</button>@if ($hasFilters)<a href="{{ url()->current() }}" class="flex h-10 flex-1 items-center justify-center rounded-lg border border-white/[0.08] px-4 text-[9px] font-bold uppercase tracking-wider text-slate-400 transition hover:bg-white/[0.05] hover:text-white sm:flex-none">{{ __('Clear') }}</a>@endif</div>
                        </form>
                    </div>
                </div>

                <div id="deposits-table-content">
                    <div class="hidden overflow-x-auto md:block">
                        <table class="w-full min-w-[780px] text-left">
                            <thead class="border-b border-white/[0.05] bg-black/10 text-[9px] font-bold uppercase tracking-[0.15em] text-slate-600"><tr><th class="px-5 py-3">{{ __('Deposit') }}</th><th class="px-4 py-3">{{ __('Amount') }}</th><th class="px-4 py-3">{{ __('Source') }}</th><th class="px-4 py-3">{{ __('Date') }}</th><th class="px-5 py-3 text-right">{{ __('Action') }}</th></tr></thead>
                            <tbody class="divide-y divide-white/[0.05]">
                                @forelse ($deposits as $deposit)
                                    @php $shortReference = strlen($deposit->transaction_reference) > 14 ? substr($deposit->transaction_reference, 0, 7) . '…' . substr($deposit->transaction_reference, -5) : $deposit->transaction_reference; @endphp
                                    <tr class="transition hover:bg-white/[0.025]">
                                        <td class="px-5 py-3.5"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border {{ $scopeData[4] }}">↓</span><div><a href="{{ route('user.deposits.view', $deposit->transaction_reference) }}" class="font-mono text-xs font-bold text-slate-200 transition hover:text-amber-400">{{ $shortReference }}</a>@if ($deposit->transaction_hash)<p class="mt-0.5 max-w-40 truncate font-mono text-[9px] text-slate-600" title="{{ $deposit->transaction_hash }}">{{ $deposit->transaction_hash }}</p>@endif</div></div></td>
                                        <td class="px-4 py-3.5"><div class="font-mono text-xs font-bold text-white">{{ number_format($deposit->amount, $decimalPlaces) }} {{ $currency }}</div>@if ($deposit->currency && $deposit->currency !== $currency)<div class="mt-0.5 font-mono text-[9px] text-slate-600">≈ {{ number_format($deposit->converted_amount, 6) }} {{ $deposit->currency }}</div>@endif</td>
                                        <td class="px-4 py-3.5 text-xs font-semibold text-slate-300">{{ $deposit->gatewayName() }}</td>
                                        <td class="px-4 py-3.5 text-[10px] text-slate-500"><span class="block text-slate-300">{{ $deposit->created_at->format('M d, Y') }}</span>{{ $deposit->created_at->format('H:i') }}</td>
                                        <td class="px-5 py-3.5 text-right"><a href="{{ route('user.deposits.view', $deposit->transaction_reference) }}" aria-label="{{ __('View deposit :reference', ['reference' => $deposit->transaction_reference]) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-white/[0.08] text-slate-500 transition hover:bg-white/[0.05] hover:text-white"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-5 py-14 text-center"><div class="text-sm font-semibold text-slate-400">{{ __('No :status deposits found', ['status' => strtolower(__($scopeName))]) }}</div><p class="mt-1 text-[10px] text-slate-600">{{ $hasFilters ? __('Try changing or clearing your filters.') : __('There are no records in this status yet.') }}</p></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="divide-y divide-white/[0.06] md:hidden">
                        @forelse ($deposits as $deposit)
                            <a href="{{ route('user.deposits.view', $deposit->transaction_reference) }}" class="block p-4 transition hover:bg-white/[0.025]">
                                <div class="flex items-start justify-between gap-4"><div class="flex min-w-0 items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border {{ $scopeData[4] }}">↓</span><div class="min-w-0"><p class="truncate text-xs font-bold text-slate-200">{{ $deposit->gatewayName() }}</p><p class="mt-0.5 truncate font-mono text-[9px] text-slate-600">{{ $deposit->transaction_reference }}</p></div></div><div class="shrink-0 text-right"><p class="font-mono text-xs font-bold text-white">{{ number_format($deposit->amount, $decimalPlaces) }} {{ $currency }}</p><p class="mt-1 text-[9px] font-bold uppercase {{ $scopeData[2] }}">{{ __($scopeName) }}</p></div></div>
                                <div class="mt-3 flex items-center justify-between border-t border-white/[0.05] pt-2 text-[9px] text-slate-600">@if ($deposit->currency && $deposit->currency !== $currency)<span>≈ {{ number_format($deposit->converted_amount, 6) }} {{ $deposit->currency }}</span>@else<span>{{ __('Deposit') }}</span>@endif<time datetime="{{ $deposit->created_at->toIso8601String() }}">{{ $deposit->created_at->format('M d, Y · H:i') }}</time></div>
                            </a>
                        @empty
                            <div class="p-12 text-center"><div class="text-sm font-semibold text-slate-400">{{ __('No :status deposits found', ['status' => strtolower(__($scopeName))]) }}</div><p class="mt-1 text-[10px] text-slate-600">{{ $hasFilters ? __('Try changing or clearing your filters.') : __('There are no records in this status yet.') }}</p></div>
                        @endforelse
                    </div>

                    @if ($deposits->hasPages())<div class="ajax-pagination border-t border-white/[0.07] p-4 sm:p-5">{{ $deposits->links('templates.york.blades.partials.pagination') }}</div>@endif
                </div>
            </section>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).on('click', '.ajax-pagination a', function(event) {
                event.preventDefault();
                const url = $(this).attr('href');
                const spinner = $('#deposits-loading-spinner');
                spinner.removeClass('hidden').addClass('flex');
                $.get(url).done(function(data) {
                    const content = $(data).find('#deposits-table-content').html();
                    if (content) { $('#deposits-table-content').html(content); window.history.replaceState({}, '', url); }
                }).fail(function() {
                    if (typeof toastNotification === 'function') toastNotification({{ Illuminate\Support\Js::from(__('Could not load deposits. Please try again.')) }}, 'error');
                }).always(function() { spinner.addClass('hidden').removeClass('flex'); });
            });
        });
    </script>
@endsection
