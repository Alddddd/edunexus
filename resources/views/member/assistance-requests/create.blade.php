@extends('layouts.dashboard')

@section('title', 'Request Assistance')

@section('content')

@php
    $programOptions = $programs->map(fn ($program) => [
        'id' => (string) $program->id,
        'name' => $program->program_name,
        'maximum_amount' => (float) $program->maximum_amount,
        'maximum_label' => '₱' . number_format($program->maximum_amount, 2),
    ])->values();
@endphp

<div class="w-full min-w-0 max-w-5xl space-y-6 text-ui-anchor">
    <x-page-header
        title="Request Assistance"
        eyebrow="Member Request"
        description="Submit a cooperative assistance request for admin review and approval." />

    <x-form-card
        title="Assistance Request Details"
        description="Choose an active program and keep the requested amount within the program ceiling.">
        <form action="{{ route('member.assistance-requests.store') }}"
              method="POST"
              class="space-y-8"
              data-confirm
              data-confirm-title="Submit assistance request?"
              data-confirm-message="This will send your request to the cooperative admin review queue. You can still edit or cancel it while it remains pending."
              data-confirm-button="Submit request"
              data-confirm-tone="success"
              data-loading-text="Submitting request..."
              data-loader-title="Submitting assistance request..."
              data-loader-message="Sending your request to the cooperative review queue and notifying administrators.">
            @csrf

            <x-form-section
                title="Program and Amount"
                description="Program rules determine the maximum request amount and eligible merchant category.">
                <div x-data="assistanceProgramSelector(@js($programOptions), @js((string) old('program_id', '')))" @click.outside="closeSuggestions()">
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Assistance Program
                    </label>

                    <select name="program_id"
                            id="program_id"
                            x-ref="select"
                            x-model="selectedId"
                            class="hidden"
                            tabindex="-1">
                        <option value="">Select Program</option>

                        @foreach($programs as $program)
                            <option value="{{ $program->id }}"
                                    data-maximum-amount="{{ $program->maximum_amount }}"
                                    @selected(old('program_id') == $program->id)>
                                {{ $program->program_name }} &middot; Max ₱{{ number_format($program->maximum_amount, 2) }}
                            </option>
                        @endforeach
                    </select>

                    <div class="relative">
                        <input type="text"
                               x-ref="input"
                               x-model="query"
                               @focus="open = true"
                               @click="open = true"
                               @input="handleInput()"
                               @keydown.escape="open = false"
                               placeholder="Search assistance programs"
                               autocomplete="off"
                               class="w-full rounded-xl border-slate-300 pr-11 focus:border-teal-500 focus:ring-teal-500"
                               required>

                        <button type="button"
                                @mousedown.prevent
                                @click="toggleSuggestions()"
                                class="absolute inset-y-0 right-0 flex w-11 items-center justify-center rounded-r-xl text-ui-subtext transition hover:text-ui-action"
                                aria-label="Toggle program suggestions">
                            <x-icon name="chevron-down" size="h-4 w-4" />
                        </button>

                        <div x-cloak
                             x-show="open"
                             x-transition.origin.top
                             class="absolute z-30 mt-2 max-h-64 w-full overflow-y-auto rounded-2xl border border-ui-border bg-ui-surface p-2 shadow-xl shadow-ui-anchor/10">
                            <template x-if="filteredPrograms().length">
                                <div class="space-y-1">
                                    <template x-for="program in filteredPrograms()" :key="program.id">
                                        <button type="button"
                                                @mousedown.prevent
                                                @click="selectProgram(program)"
                                                class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-left transition hover:bg-ui-canvas">
                                            <span class="min-w-0">
                                                <span class="block truncate text-sm font-semibold text-ui-text" x-text="program.name"></span>
                                                <span class="mt-0.5 block text-xs font-medium text-ui-subtext" x-text="`Maximum ${program.maximum_label}`"></span>
                                            </span>

                                            <x-icon name="check" size="h-4 w-4 shrink-0 text-ui-action" />
                                        </button>
                                    </template>
                                </div>
                            </template>

                            <template x-if="!filteredPrograms().length">
                                <p class="px-3 py-2 text-sm text-ui-subtext">
                                    No matching assistance program found.
                                </p>
                            </template>
                        </div>
                    </div>

                    <p id="program-maximum-helper" class="mt-2 text-sm text-ui-subtext">
                        Select a program to see the maximum request amount.
                    </p>

                    @error('program_id')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Requested Amount
                    </label>

                    <input type="number"
                           step="0.01"
                           name="requested_amount"
                           id="requested_amount"
                           value="{{ old('requested_amount') }}"
                           class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                           required>

                    <p id="requested-amount-warning" class="mt-2 hidden text-sm font-medium text-ui-danger"></p>

                    @error('requested_amount')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </x-form-section>

            <x-form-section
                title="Request Notes"
                description="Optional context for cooperative review.">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Reason / Notes
                    </label>

                    <textarea name="reason"
                              rows="4"
                              class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">{{ old('reason') }}</textarea>
                </div>
            </x-form-section>

            <div class="flex flex-col gap-3 border-t border-ui-border/80 pt-6 sm:flex-row sm:items-center">
                <button type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-ui-action px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor sm:w-auto">
                    Submit Request
                </button>

                <a href="{{ route('member.dashboard') }}"
                   class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-ui-border bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas sm:w-auto">
                    Back to Dashboard
                </a>
            </div>
        </form>
    </x-form-card>
</div>

<script>
    window.assistanceProgramSelector = function (programs, initialId = '') {
        return {
            open: false,
            query: '',
            selectedId: initialId || '',
            programs,
            init() {
                const selectedProgram = this.programs.find((program) => program.id === this.selectedId);

                if (selectedProgram) {
                    this.query = selectedProgram.name;
                }

                this.$nextTick(() => {
                    this.validateInput();
                    this.dispatchProgramChange();
                });
            },
            filteredPrograms() {
                const search = this.query.toLowerCase().trim();

                if (!search) {
                    return this.programs;
                }

                return this.programs.filter((program) =>
                    program.name.toLowerCase().includes(search)
                );
            },
            handleInput() {
                const exactMatch = this.programs.find((program) =>
                    program.name.toLowerCase() === this.query.toLowerCase().trim()
                );

                this.selectedId = exactMatch ? exactMatch.id : '';
                this.open = true;
                this.validateInput();
                this.dispatchProgramChange();
            },
            selectProgram(program) {
                this.selectedId = program.id;
                this.query = program.name;
                this.open = false;
                this.validateInput();
                this.dispatchProgramChange();
            },
            toggleSuggestions() {
                this.open = !this.open;
            },
            closeSuggestions() {
                this.open = false;
            },
            validateInput() {
                if (!this.$refs.input) {
                    return;
                }

                const hasTypedValue = this.query.trim().length > 0;
                this.$refs.input.setCustomValidity(hasTypedValue && !this.selectedId
                    ? 'Select an assistance program from the list.'
                    : ''
                );
            },
            dispatchProgramChange() {
                this.$nextTick(() => {
                    this.$refs.select?.dispatchEvent(new Event('change', { bubbles: true }));
                });
            },
        };
    };

    const programSelect = document.getElementById('program_id');
    const amountInput = document.getElementById('requested_amount');
    const maximumHelper = document.getElementById('program-maximum-helper');
    const amountWarning = document.getElementById('requested-amount-warning');

    const formatPeso = (amount) => `₱${Number(amount).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;

    const validateRequestedAmount = () => {
        if (!amountInput || !amountWarning) {
            return;
        }

        const maximumAmount = Number(amountInput.max || 0);
        const requestedAmount = Number(amountInput.value || 0);

        if (maximumAmount > 0 && requestedAmount > maximumAmount) {
            const message = `Requested amount exceeds the selected program maximum of ${formatPeso(maximumAmount)}.`;
            amountInput.setCustomValidity(message);
            amountWarning.textContent = message;
            amountWarning.classList.remove('hidden');
            return;
        }

        amountInput.setCustomValidity('');
        amountWarning.textContent = '';
        amountWarning.classList.add('hidden');
    };

    const updateProgramMaximum = () => {
        const selectedOption = programSelect?.selectedOptions?.[0];
        const maximumAmount = selectedOption?.dataset?.maximumAmount;

        if (!amountInput || !maximumHelper) {
            return;
        }

        if (maximumAmount) {
            amountInput.max = maximumAmount;
            maximumHelper.textContent = `Maximum request amount: ${formatPeso(maximumAmount)}`;
            validateRequestedAmount();
            return;
        }

        amountInput.removeAttribute('max');
        maximumHelper.textContent = 'Select a program to see the maximum request amount.';
        validateRequestedAmount();
    };

    amountInput?.addEventListener('input', validateRequestedAmount);

    programSelect?.addEventListener('change', updateProgramMaximum);
    updateProgramMaximum();
</script>

@endsection
