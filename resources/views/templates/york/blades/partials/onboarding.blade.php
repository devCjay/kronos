<div id="onboarding-modal"
    class="pointer-events-none fixed inset-0 z-[98] flex items-center justify-center overflow-y-auto bg-[#0b0e11]/90 p-3 opacity-0 backdrop-blur-xl transition duration-300 sm:p-6"
    role="dialog" aria-modal="true" aria-labelledby="onboarding-title" aria-describedby="onboarding-description">
    <div id="onboarding-content"
        class="my-auto flex max-h-[calc(100dvh-1.5rem)] w-full max-w-4xl scale-95 flex-col overflow-hidden rounded-2xl border border-white/[0.1] bg-[#11161c] shadow-[0_30px_100px_rgba(0,0,0,.75)] transition duration-300 sm:max-h-[min(90vh,800px)]">

        <header class="relative shrink-0 border-b border-white/[0.07] px-5 py-5 sm:px-7 sm:py-6">
            <div class="pointer-events-none absolute right-0 top-0 h-40 w-40 -translate-y-1/2 translate-x-1/3 rounded-full bg-amber-500/[0.08] blur-3xl"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-amber-400/15 bg-amber-400/[0.06] px-2.5 py-1 text-[9px] font-bold uppercase tracking-[0.18em] text-amber-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-400 shadow-[0_0_8px_rgba(247,166,0,.75)]"></span>
                        {{ __('Trading preferences') }}
                    </div>
                    <h2 id="onboarding-title" class="font-heading text-xl font-black tracking-tight text-white sm:text-2xl">
                        {{ __('Choose your risk preference') }}
                    </h2>
                    <p id="onboarding-description" class="mt-1 max-w-2xl text-xs leading-5 text-slate-400 sm:text-sm">
                        {{ __('Tell us how you generally approach market risk. This preference helps personalize your experience and does not activate a bot or guarantee performance.') }}
                    </p>
                </div>
                <button type="button" id="onboarding-close" aria-label="{{ __('Close onboarding') }}"
                    class="flex h-9 w-9 shrink-0 cursor-pointer items-center justify-center rounded-xl border border-white/[0.09] bg-white/[0.035] text-slate-400 transition hover:bg-white/[0.07] hover:text-white focus:outline-none focus:ring-2 focus:ring-amber-400/40">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </header>

        <form id="onboarding-form" action="{{ route('user.onboarding') }}" method="POST" class="flex min-h-0 flex-1 flex-col">
            @csrf
            <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-5 [scrollbar-color:rgba(255,255,255,.15)_transparent] [scrollbar-width:thin] sm:px-7 sm:py-6">
                <fieldset>
                    <legend class="sr-only">{{ __('Select a risk preference') }}</legend>
                    <div id="strategy-cards-grid" class="grid grid-cols-1 gap-3 md:grid-cols-3">
                        @php
                            $riskProfiles = [
                                'conservative' => [
                                    'label' => __('Conservative'),
                                    'risk' => __('Lower risk preference'),
                                    'description' => __('You generally prioritize capital preservation and prefer smaller exposure to market volatility.'),
                                    'focus' => __('Capital preservation'),
                                    'pace' => __('Measured'),
                                    'color' => 'emerald',
                                ],
                                'balanced' => [
                                    'label' => __('Balanced'),
                                    'risk' => __('Moderate risk preference'),
                                    'description' => __('You are comfortable balancing growth opportunities with measured risk controls.'),
                                    'focus' => __('Risk and growth balance'),
                                    'pace' => __('Moderate'),
                                    'color' => 'amber',
                                ],
                                'growth' => [
                                    'label' => __('Growth'),
                                    'risk' => __('Higher risk preference'),
                                    'description' => __('You accept greater price movement while pursuing more aggressive growth opportunities.'),
                                    'focus' => __('Growth opportunities'),
                                    'pace' => __('Active'),
                                    'color' => 'rose',
                                ],
                            ];
                        @endphp

                        @foreach ($riskProfiles as $value => $profile)
                            <label class="group relative cursor-pointer">
                                <input type="radio" name="risk_profile" value="{{ $value }}" class="strategy-radio peer sr-only" {{ $value === 'balanced' ? 'checked' : '' }}>
                                <div data-strategy="{{ $value }}"
                                    class="strategy-card flex h-full min-h-52 flex-col rounded-xl border p-4 text-left transition duration-200 peer-focus-visible:ring-2 peer-focus-visible:ring-amber-400/50 {{ $value === 'balanced' ? 'border-amber-400/50 bg-amber-400/[0.07] ring-1 ring-amber-400/20' : 'border-white/[0.08] bg-white/[0.025] hover:border-white/[0.16] hover:bg-white/[0.045]' }}">
                                    <div class="flex items-start justify-between gap-3">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-lg border {{ $value === 'conservative' ? 'border-emerald-400/20 bg-emerald-400/[0.08] text-emerald-400' : ($value === 'balanced' ? 'border-amber-400/20 bg-amber-400/[0.08] text-amber-400' : 'border-rose-400/20 bg-rose-400/[0.08] text-rose-400') }}">
                                            @if ($value === 'conservative')
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10zM9 12l2 2 4-4"/></svg>
                                            @elseif ($value === 'balanced')
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 19h16M6 16l4-5 3 3 5-7"/></svg>
                                            @else
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                                            @endif
                                        </span>
                                        <span class="strategy-indicator flex h-4 w-4 items-center justify-center rounded-full border {{ $value === 'balanced' ? 'border-amber-400 bg-amber-400/20' : 'border-white/20' }}">
                                            <span class="h-2 w-2 rounded-full {{ $value === 'balanced' ? 'bg-amber-400 opacity-100' : 'opacity-0' }}"></span>
                                        </span>
                                    </div>

                                    <div class="mt-4">
                                        <div class="text-[9px] font-bold uppercase tracking-[0.14em] {{ $value === 'conservative' ? 'text-emerald-400' : ($value === 'balanced' ? 'text-amber-400' : 'text-rose-400') }}">{{ $profile['risk'] }}</div>
                                        <h3 class="mt-1 text-base font-bold text-white">{{ $profile['label'] }}</h3>
                                        <p class="mt-1.5 text-[11px] leading-5 text-slate-400">{{ $profile['description'] }}</p>
                                    </div>

                                    <div class="mt-auto grid grid-cols-2 gap-2 border-t border-white/[0.06] pt-3 text-[9px]">
                                        <div><span class="block uppercase tracking-wider text-slate-600">{{ __('Focus') }}</span><span class="mt-0.5 block font-semibold text-slate-200">{{ $profile['focus'] }}</span></div>
                                        <div><span class="block uppercase tracking-wider text-slate-600">{{ __('Style') }}</span><span class="mt-0.5 block font-semibold text-slate-200">{{ $profile['pace'] }}</span></div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <div class="mt-4 rounded-xl border border-white/[0.07] bg-[#0b0e11]/60 p-4 sm:flex sm:items-center sm:justify-between sm:gap-6">
                    <div class="flex items-start gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-400/[0.07] text-amber-400"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg></span>
                        <div><div class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-300">{{ __('Selected preference') }}</div><div id="selected-profile-name" class="mt-0.5 text-sm font-bold text-amber-400">{{ __('Balanced') }}</div><p id="selected-profile-summary" class="mt-1 max-w-xl text-[10px] leading-4 text-slate-500">{{ $riskProfiles['balanced']['description'] }}</p></div>
                    </div>
                    <div class="mt-3 shrink-0 rounded-lg border border-white/[0.06] bg-white/[0.025] px-3 py-2 text-[9px] leading-4 text-slate-500 sm:mt-0 sm:max-w-48">{{ __('This preference does not start trades. Bot activation and terms remain separate.') }}</div>
                </div>
            </div>

            <footer class="flex shrink-0 flex-col gap-3 border-t border-white/[0.07] bg-[#11161c]/95 px-5 py-4 backdrop-blur-xl sm:flex-row sm:items-center sm:justify-between sm:px-7">
                <p class="flex items-center gap-2 text-[10px] leading-4 text-slate-500"><svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>{{ __('Your selection is stored securely with your account preferences.') }}</p>
                <button type="submit" id="onboarding-submit-btn"
                    class="group flex min-h-11 w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 px-6 text-sm font-extrabold text-black shadow-[0_0_24px_rgba(247,166,0,.18)] transition hover:-translate-y-0.5 hover:from-amber-300 hover:to-amber-400 focus:outline-none focus:ring-4 focus:ring-amber-400/20 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto">
                    <span id="onboarding-submit-label">{{ __('Save preference') }}</span>
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </footer>
        </form>
    </div>
</div>

@push('scripts')
    @php
        $onboardingProfiles = collect($riskProfiles)->map(fn ($profile) => [
            'label' => $profile['label'],
            'description' => $profile['description'],
        ])->all();
    @endphp
    <script>
        $(document).ready(function() {
            const profiles = {{ Illuminate\Support\Js::from($onboardingProfiles) }};
            const modal = $('#onboarding-modal');
            const content = $('#onboarding-content');
            const closeButton = $('#onboarding-close');
            let previouslyFocused = null;

            function updateSelection(strategy) {
                const profile = profiles[strategy] || profiles.balanced;
                $('#selected-profile-name').text(profile.label);
                $('#selected-profile-summary').text(profile.description);

                $('.strategy-card').each(function() {
                    const selected = $(this).data('strategy') === strategy;
                    $(this).toggleClass('border-amber-400/50 bg-amber-400/[0.07] ring-1 ring-amber-400/20', selected)
                        .toggleClass('border-white/[0.08] bg-white/[0.025]', !selected);
                    $(this).find('.strategy-indicator').toggleClass('border-amber-400 bg-amber-400/20', selected).toggleClass('border-white/20', !selected);
                    $(this).find('.strategy-indicator span').toggleClass('bg-amber-400 opacity-100', selected).toggleClass('opacity-0', !selected);
                });
            }

            function openOnboarding() {
                previouslyFocused = document.activeElement;
                modal.removeClass('pointer-events-none opacity-0');
                content.removeClass('scale-95');
                $('body').addClass('overflow-hidden');
                setTimeout(() => closeButton.trigger('focus'), 200);
            }

            function closeOnboarding() {
                modal.addClass('pointer-events-none opacity-0');
                content.addClass('scale-95');
                $('body').removeClass('overflow-hidden');
                if (previouslyFocused) previouslyFocused.focus();
            }

            $(document).on('change', 'input[name="risk_profile"]', function() {
                updateSelection($(this).val());
            });

            setTimeout(openOnboarding, 400);
            closeButton.on('click', closeOnboarding);
            $(document).on('keydown', function(event) {
                if (event.key === 'Escape' && !modal.hasClass('pointer-events-none')) closeOnboarding();
            });
            modal.on('click', function(event) {
                if (event.target === this) closeOnboarding();
            });

            $('#onboarding-form').on('submit', function(event) {
                event.preventDefault();
                const form = $(this);
                const submitButton = $('#onboarding-submit-btn');
                const submitLabel = $('#onboarding-submit-label');
                const originalText = submitLabel.text();

                submitButton.prop('disabled', true);
                submitLabel.text({{ Illuminate\Support\Js::from(__('Saving...')) }});

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.status !== 'success') {
                            submitButton.prop('disabled', false);
                            submitLabel.text(originalText);
                            return;
                        }

                        closeOnboarding();
                        if (typeof toastr !== 'undefined') toastr.success(response.message || {{ Illuminate\Support\Js::from(__('Preference saved.')) }});
                        $('#drawer-item-onboarding').slideUp(250, function() { $(this).remove(); });

                        const countElement = $('#pending-items-count');
                        if (countElement.length) {
                            const nextCount = Math.max(0, (parseInt(countElement.text()) || 1) - 1);
                            countElement.text(nextCount);
                            if (nextCount === 0) $('#pending-action-notice').slideUp(300, function() { $(this).remove(); });
                        }
                    },
                    error: function(xhr) {
                        submitButton.prop('disabled', false);
                        submitLabel.text(originalText);
                        const message = xhr.responseJSON?.message || {{ Illuminate\Support\Js::from(__('Could not save your preference. Please try again.')) }};
                        if (typeof toastr !== 'undefined') toastr.error(message);
                    }
                });
            });

            updateSelection($('input[name="risk_profile"]:checked').val());
        });
    </script>
@endpush
