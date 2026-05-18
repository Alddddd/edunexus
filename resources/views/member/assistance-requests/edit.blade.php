@extends('layouts.dashboard')

@section('title', 'Edit Assistance Request')

@section('content')

<div class="max-w-3xl">
    <x-page-header
        title="Edit Assistance Request"
        eyebrow="Pending Request"
        description="Update your pending request before cooperative approval. Approved requests cannot be changed to protect QR, claim, settlement, and proof integrity." />

    <x-form-card
        title="Pending Request Details"
        description="Only pending requests can be edited or cancelled.">
        <form action="{{ route('member.assistance-requests.update', $request) }}"
              method="POST"
              class="space-y-6">
            @csrf
            @method('PUT')

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
                                @selected(old('program_id', $request->program_id) == $program->id)>
                            {{ $program->program_name }} / Max PHP {{ number_format($program->maximum_amount, 2) }}
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
                       value="{{ old('requested_amount', $request->requested_amount) }}"
                       class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                       required>

                @error('requested_amount')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                    Reason / Notes
                </label>

                <textarea name="reason"
                          rows="4"
                          class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">{{ old('reason', $request->reason) }}</textarea>
            </div>

            <div class="flex flex-col gap-3 border-t border-ui-border/80 pt-6 sm:flex-row sm:items-center">
                <button type="submit"
                        class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                    Update Request
                </button>

                <a href="{{ route('member.dashboard') }}"
                   class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-border bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas">
                    Cancel
                </a>
            </div>
        </form>

        <form method="POST"
              action="{{ route('member.assistance-requests.destroy', $request) }}"
              class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 p-5"
              data-confirm
              data-confirm-title="Cancel this pending request?"
              data-confirm-message="This will delete the pending assistance request. Approved requests cannot be deleted."
              data-confirm-button="Cancel request"
              data-confirm-tone="danger"
              data-loading-text="Cancelling request..."
              data-loader-title="Cancelling request..."
              data-loader-message="Removing the pending assistance request from the review queue.">
            @csrf
            @method('DELETE')

            <p class="font-semibold text-rose-800">
                Cancel Pending Request
            </p>

            <p class="mt-1 text-sm text-rose-700">
                Use this only if you no longer want this request reviewed by the cooperative.
            </p>

            <button type="submit"
                    class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-danger px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700">
                Cancel Request
            </button>
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
            maximumHelper.textContent = `Maximum request amount: PHP ${Number(maximumAmount).toLocaleString(undefined, {
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
