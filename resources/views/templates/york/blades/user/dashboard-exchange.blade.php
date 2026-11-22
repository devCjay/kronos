@extends('templates.york.blades.layouts.user')

@section('content')
    @include('templates.york.blades.partials.dashboard-partials')

    @php
        $currencySymbol = getSetting('currency_symbol', '$');
        $netFlow = $movement_stats['net_cashflow'];
        $botModuleEnabled = moduleEnabled('trading_bot_module');
    @endphp

    <div class="exchange-dashboard space-y-4 pb-10">
        {{-- Exchange-style account header --}}
        <section class="relative overflow-hidden rounded-2xl border border-white/[0.08] bg-[#111318] p-5 shadow-[0_18px_50px_rgba(0,0,0,.18)] md:p-7">
            <div class="pointer-events-none absolute -right-24 -top-24 h-64 w-64 rounded-full bg-amber-400/[0.08] blur-3xl"></div>
            <div class="relative flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <div class="mb-3 flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.24em] text-slate-500">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_10px_rgba(52,211,153,.8)]"></span>
                        {{ __('Unified Account') }} · {{ __('Live') }}
                    </div>
                    <p class="mb-4 text-sm font-semibold text-slate-300">{{ __('Welcome back, :name', ['name' => Auth::user()->first_name ?: Auth::user()->username]) }}</p>
                    <p class="text-xs font-semibold text-slate-400">{{ __('Total Equity') }}</p>
                    <div class="mt-1 flex flex-wrap items-baseline gap-3">
                        <h1 class="text-3xl font-black tracking-tight text-white sm:text-5xl">
                            {{ $currencySymbol }}{{ number_format($total_equity, 2) }}
                        </h1>
                        <span class="text-xs font-bold text-slate-500">{{ getSetting('currency', 'USD') }}</span>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-x-7 gap-y-3 text-xs">
                        <div><span class="text-slate-500">{{ __('Available') }}</span><strong class="ml-2 text-white">{{ showAmount($balance) }}</strong></div>
                        @if ($botModuleEnabled)
                            <div><span class="text-slate-500">{{ __('In Bots') }}</span><strong class="ml-2 text-white">{{ showAmount($bot_capital) }}</strong></div>
                        @endif
                        <div><span class="text-slate-500">{{ __('Month Net Flow') }}</span><strong class="ml-2 {{ $netFlow >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">{{ $netFlow >= 0 ? '+' : '' }}{{ showAmount($netFlow) }}</strong></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 sm:flex">
                    <a href="{{ route('user.deposits.new') }}" class="flex min-h-11 items-center justify-center gap-2 rounded-xl bg-amber-400 px-5 text-center text-xs font-black uppercase tracking-wider text-black transition hover:bg-amber-300 focus:outline-none focus:ring-4 focus:ring-amber-400/20"><span class="text-base leading-none">+</span>{{ __('Deposit') }}</a>
                    <a href="{{ route('user.withdrawals.new') }}" class="flex min-h-11 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-5 text-center text-xs font-bold uppercase tracking-wider text-white transition hover:bg-white/[0.08] focus:outline-none focus:ring-4 focus:ring-white/10">{{ __('Withdraw') }}</a>
                    @if ($botModuleEnabled)
                        <a href="{{ route('user.trading-bots.index') }}" class="col-span-2 flex min-h-11 items-center justify-center rounded-xl border border-amber-400/30 bg-amber-400/[0.08] px-5 text-center text-xs font-bold uppercase tracking-wider text-amber-300 transition hover:bg-amber-400/[0.14] focus:outline-none focus:ring-4 focus:ring-amber-400/10">{{ __('Explore Trading Bots') }}</a>
                    @endif
                </div>
            </div>
        </section>

        {{-- Primary market/account workspace --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">
            <section class="rounded-2xl border border-white/[0.08] bg-[#111318] xl:col-span-8">
                <div class="flex flex-col gap-3 border-b border-white/[0.07] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-sm font-black text-white">{{ __('Account Activity') }}</h2>
                        <p class="mt-1 text-[10px] uppercase tracking-widest text-slate-500">{{ __('Completed credits and debits over time') }}</p>
                    </div>
                    <div class="flex max-w-full overflow-x-auto rounded-lg bg-black/30 p-1" id="exchange-chart-filters" aria-label="{{ __('Account activity period') }}">
                        @foreach (['7' => '7D', '30' => '30D', '90' => '90D', '365' => '1Y', 'all' => __('All')] as $days => $label)
                            <button type="button" data-days="{{ $days }}" aria-pressed="{{ $days === '7' ? 'true' : 'false' }}" class="chart-range shrink-0 cursor-pointer rounded-md px-3 py-1.5 text-[10px] font-bold text-slate-500 transition hover:text-white focus:outline-none focus:ring-2 focus:ring-amber-400/30 {{ $days === '7' ? 'bg-white/[0.08] text-white' : '' }}">{{ $label }}</button>
                        @endforeach
                    </div>
                </div>
                <div class="relative p-3 sm:p-5">
                    <div id="exchange-chart-loading" class="pointer-events-none absolute inset-0 z-10 hidden items-center justify-center bg-[#111318]/70 backdrop-blur-sm"><span class="h-6 w-6 animate-spin rounded-full border-2 border-white/10 border-t-amber-400" aria-label="{{ __('Loading chart') }}"></span></div>
                    <div id="exchangePortfolioChart" class="h-[260px] sm:h-[300px]"></div>
                    <div id="exchange-chart-error" class="hidden py-16 text-center text-xs text-rose-400">{{ __('Account activity could not be loaded. Please try again.') }}</div>
                </div>
            </section>

            <aside class="rounded-2xl border border-white/[0.08] bg-[#111318] xl:col-span-4">
                <div class="border-b border-white/[0.07] px-5 py-4">
                    <h2 class="text-sm font-black text-white">{{ __('Asset Allocation') }}</h2>
                    <p class="mt-1 text-[10px] uppercase tracking-widest text-slate-500">{{ __('Unified account distribution') }}</p>
                </div>
                <div class="p-4"><div id="exchangeAllocationChart" class="h-[220px]"></div></div>
                <div class="grid {{ $botModuleEnabled ? 'grid-cols-2' : 'grid-cols-1' }} gap-px border-t border-white/[0.07] bg-white/[0.07]">
                    <div class="bg-[#111318] p-4"><p class="text-[9px] uppercase tracking-widest text-slate-500">{{ __('Wallet') }}</p><p class="mt-1 text-sm font-bold text-white">{{ showAmount($balance) }}</p></div>
                    @if ($botModuleEnabled)
                        <div class="bg-[#111318] p-4"><p class="text-[9px] uppercase tracking-widest text-slate-500">{{ __('Bot Capital') }}</p><p class="mt-1 text-sm font-bold text-amber-300">{{ showAmount($bot_capital) }}</p></div>
                    @endif
                </div>
            </aside>
        </div>

        {{-- Cash flow ticker cards --}}
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                [__('Monthly Deposits'), $deposits_month, 'text-emerald-400', $deposits_pending . ' ' . __('pending')],
                [__('Monthly Withdrawals'), $withdrawals_month_amount, 'text-rose-400', $withdrawals_pending . ' ' . __('pending')],
                [__('Deposit Average'), $movement_stats['deposits']['avg_size'], 'text-white', $movement_stats['deposits']['most_used_method']],
                [__('Network Fees'), $movement_stats['total_fees_month'], 'text-amber-300', __('This month')],
            ] as [$label, $value, $color, $caption])
                <div class="rounded-xl border border-white/[0.07] bg-[#111318] p-4">
                    <p class="truncate text-[9px] font-bold uppercase tracking-[0.16em] text-slate-500">{{ $label }}</p>
                    <p class="mt-2 text-lg font-black {{ $color }}">{{ showAmount($value) }}</p>
                    <p class="mt-1 truncate text-[10px] text-slate-600">{{ $caption }}</p>
                </div>
            @endforeach
        </div>

        <div>
            {{-- Recent ledger --}}
            <section class="overflow-hidden rounded-2xl border border-white/[0.08] bg-[#111318]">
                <div class="flex items-center justify-between border-b border-white/[0.07] px-5 py-4">
                    <div><h2 class="text-sm font-black text-white">{{ __('Recent Transactions') }}</h2><p class="mt-1 text-[10px] uppercase tracking-widest text-slate-500">{{ __('Account ledger') }}</p></div>
                    <a href="{{ route('user.transactions') }}" class="text-[10px] font-bold uppercase tracking-wider text-amber-300 hover:text-amber-200">{{ __('View All') }}</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[620px] text-left">
                        <thead class="border-b border-white/[0.05] text-[9px] uppercase tracking-widest text-slate-600">
                            <tr><th class="px-5 py-3">{{ __('Description') }}</th><th class="px-4 py-3">{{ __('Reference') }}</th><th class="px-4 py-3">{{ __('Date') }}</th><th class="px-5 py-3 text-right">{{ __('Amount') }}</th></tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.05]">
                            @forelse ($recent_transactions_hub as $tx)
                                <tr class="transition hover:bg-white/[0.025]">
                                    <td class="px-5 py-3"><div class="flex items-center gap-3"><span class="flex h-7 w-7 items-center justify-center rounded-full {{ $tx->type === 'credit' ? 'bg-emerald-400/10 text-emerald-400' : 'bg-rose-400/10 text-rose-400' }}">{{ $tx->type === 'credit' ? '↓' : '↑' }}</span><span class="max-w-[230px] truncate text-xs font-semibold text-slate-200">{{ $tx->description }}</span></div></td>
                                    <td class="px-4 py-3 font-mono text-[10px] text-slate-500">{{ $tx->reference }}</td>
                                    <td class="px-4 py-3 text-[10px] text-slate-500">{{ $tx->created_at->format('M d, H:i') }}</td>
                                    <td class="px-5 py-3 text-right text-xs font-bold {{ $tx->type === 'credit' ? 'text-emerald-400' : 'text-rose-400' }}">{{ $tx->type === 'credit' ? '+' : '-' }}{{ showAmount($tx->amount) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-12 text-center text-xs text-slate-600">{{ __('No recent transactions') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const money = (value) => '{{ $currencySymbol }}' + new Intl.NumberFormat('en-US', { maximumFractionDigits: 2 }).format(value || 0);
            const chartData = @json($chart_data);
            const allocation = @json($money_distribution);
            const portfolioChartElement = document.querySelector('#exchangePortfolioChart');
            const allocationChartElement = document.querySelector('#exchangeAllocationChart');
            const chartError = document.querySelector('#exchange-chart-error');
            const chartLoading = document.querySelector('#exchange-chart-loading');

            if (typeof ApexCharts === 'undefined') {
                portfolioChartElement.classList.add('hidden');
                chartError.classList.remove('hidden');
                return;
            }

            const portfolioChart = new ApexCharts(portfolioChartElement, {
                chart: { type: 'area', height: 300, toolbar: { show: false }, zoom: { enabled: false }, background: 'transparent' },
                series: [{ name: '{{ __('Credits') }}', data: chartData.credits }, { name: '{{ __('Debits') }}', data: chartData.debits }],
                colors: ['#22c55e', '#f43f5e'], stroke: { curve: 'smooth', width: 2 }, dataLabels: { enabled: false },
                fill: { type: 'gradient', gradient: { opacityFrom: .24, opacityTo: .02, stops: [0, 95] } },
                xaxis: { categories: chartData.labels, axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { colors: '#64748b', fontSize: '10px' } } },
                yaxis: { labels: { formatter: money, style: { colors: '#64748b', fontSize: '10px' } } },
                grid: { borderColor: 'rgba(255,255,255,.05)', strokeDashArray: 4 }, legend: { labels: { colors: '#94a3b8' } }, tooltip: { theme: 'dark', y: { formatter: money } }
            });
            portfolioChart.render();

            new ApexCharts(allocationChartElement, {
                chart: { type: 'donut', height: 220, background: 'transparent' }, series: Object.values(allocation), labels: Object.keys(allocation),
                colors: ['#f7a600', '#22c55e', '#3b82f6'], stroke: { width: 0 }, dataLabels: { enabled: false },
                legend: { position: 'bottom', labels: { colors: '#94a3b8' }, fontSize: '10px' },
                plotOptions: { pie: { donut: { size: '76%', labels: { show: true, total: { show: true, label: '{{ __('Equity') }}', color: '#64748b', formatter: () => money({{ (float) $total_equity }}) }, value: { color: '#fff', fontWeight: 800, formatter: money } } } } },
                tooltip: { theme: 'dark', y: { formatter: money } }
            }).render();

            document.querySelectorAll('.chart-range').forEach(button => button.addEventListener('click', async function () {
                document.querySelectorAll('.chart-range').forEach(item => {
                    item.classList.remove('bg-white/[0.08]', 'text-white');
                    item.setAttribute('aria-pressed', 'false');
                    item.disabled = true;
                });
                this.classList.add('bg-white/[0.08]', 'text-white');
                this.setAttribute('aria-pressed', 'true');
                chartLoading.classList.remove('hidden');
                chartLoading.classList.add('flex');
                chartError.classList.add('hidden');
                try {
                    const response = await fetch(`{{ route('user.dashboard') }}?days=${this.dataset.days}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!response.ok) throw new Error(`Dashboard chart request failed: ${response.status}`);
                    const data = await response.json();
                    portfolioChart.updateOptions({ xaxis: { categories: data.labels } });
                    portfolioChart.updateSeries([{ name: '{{ __('Credits') }}', data: data.credits }, { name: '{{ __('Debits') }}', data: data.debits }]);
                } catch (error) {
                    chartError.classList.remove('hidden');
                    console.error('Dashboard chart update failed', error);
                } finally {
                    chartLoading.classList.add('hidden');
                    chartLoading.classList.remove('flex');
                    document.querySelectorAll('.chart-range').forEach(item => { item.disabled = false; });
                }
            }));

        });
    </script>
@endsection
