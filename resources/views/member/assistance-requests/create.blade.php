@extends('layouts.dashboard')

@section('title', 'Request Assistance')

@section('content')

<div class="max-w-3xl">
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
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Assistance Program
                    </label>

                    <select name="program_id"
                            id="program_id"
                            class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                            required>
                        <option value="">Select Program</option>

                        @foreach($programs as $program)
                            <option value="{{ $program->id }}"
                                    data-maximum-amount="{{ $program->maximum_amount }}"
                                    @selected(old('program_id') == $program->id)>
                                {{ $program->program_name }} &middot; Max ₱{{ number_format($program->maximum_amount, 2) }}
                            </option>
                        @endforeach
                    </select>

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
                        class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                    Submit Request
                </button>

                <a href="{{ route('member.dashboard') }}"
                   class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-border bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas">
                    Back to Dashboard
                </a>
            </div>
        </form>
    </x-form-card>
</div>

<script>
    const programSelect = document.getElementById('program_id');
    const amountInput = document.getElementById('requested_amount');
    const maximumHelper = document.getElementById('program-maximum-helper');

    const updateProgramMaximum = () => {
        const selectedOption = programSelect?.selectedOptions?.[0];
        const maximumAmount = selectedOption?.dataset?.maximumAmount;

        if (!amountInput || !maximumHelper) {
            return;
        }

        if (maximumAmount) {
            amountInput.max = maximumAmount;
            maximumHelper.textContent = `Maximum request amount: ₱${Number(maximumAmount).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            })}`;
            return;
        }

        amountInput.removeAttribute('max');
        maximumHelper.textContent = 'Select a program to see the maximum request amount.';
    };

    amountInput?.addEventListener('input', () => {
        const maximumAmount = Number(amountInput.max || 0);
        const requestedAmount = Number(amountInput.value || 0);

        if (maximumAmount > 0 && requestedAmount > maximumAmount) {
            amountInput.setCustomValidity('Requested amount cannot exceed the selected program maximum.');
            return;
        }

        amountInput.setCustomValidity('');
    });

    programSelect?.addEventListener('change', updateProgramMaximum);
    updateProgramMaximum();
</script>

@endsection
