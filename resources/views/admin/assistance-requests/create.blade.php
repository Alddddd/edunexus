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
                    class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                    required>

                <option value="">
                    Select Program
                </option>

                @foreach($programs as $program)

                    <option value="{{ $program->id }}">
                        {{ $program->program_name }}
                    </option>

                @endforeach

            </select>

        </div>

        <div>

            <label class="block text-sm font-medium text-slate-700 mb-2">
                Requested Amount
            </label>

            <input type="number"
                   step="0.01"
                   name="requested_amount"
                   class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                   required>

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

@endsection