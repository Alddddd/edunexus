@extends('layouts.dashboard')

@section('title', 'Request Assistance')

@section('content')

<div class="max-w-3xl">

    <div class="mb-8">

        <h1 class="text-3xl font-bold text-slate-800">
            Request Assistance
        </h1>

        <p class="text-slate-500 mt-2">
            Submit a cooperative assistance request for admin review and approval.
        </p>

    </div>

    <form action="{{ route('member.assistance-requests.store') }}"
          method="POST"
          class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 space-y-6">

        @csrf

        <div>

            <label class="block text-sm font-medium text-slate-700 mb-2">
                Assistance Program
            </label>

            <select name="program_id"
                    id="program_id"
                    class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                    required>

                <option value="">
                    Select Program
                </option>

                @foreach($programs as $program)

                    <option value="{{ $program->id }}"
                            data-maximum-amount="{{ $program->maximum_amount }}"
                            @selected(old('program_id') == $program->id)>
                        {{ $program->program_name }} · Max PHP {{ number_format($program->maximum_amount, 2) }}
                    </option>

                @endforeach

            </select>

            <p id="program-maximum-helper" class="mt-2 text-sm text-slate-500">
                Select a program to see the maximum request amount.
            </p>

            @error('program_id')
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror

        </div>

        <div>

            <label class="block text-sm font-medium text-slate-700 mb-2">
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
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror

        </div>

        <div>

            <label class="block text-sm font-medium text-slate-700 mb-2">
                Reason / Notes
            </label>

            <textarea name="reason"
                      rows="4"
                      class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"></textarea>

        </div>

        <div class="pt-4">

            <button type="submit"
                    class="px-6 py-3 rounded-xl bg-teal-600 text-white font-medium hover:bg-teal-700 transition">

                Submit Request

            </button>

        </div>

    </form>

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

    programSelect?.addEventListener('change', updateProgramMaximum);
    updateProgramMaximum();
</script>

@endsection
