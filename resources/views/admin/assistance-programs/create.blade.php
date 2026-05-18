@extends('layouts.dashboard')

@section('title', 'Create Assistance Program')

@section('content')

<div class="max-w-3xl">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800">
            Create Assistance Program
        </h1>

        <p class="text-slate-500 mt-2">
            Configure assistance rules for QR-based merchant claims.
        </p>
    </div>

    <form action="{{ route('admin.assistance-programs.store') }}"
          method="POST"
          class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">
                Program Name
            </label>

            <input type="text"
                   name="program_name"
                   class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                   required>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">
                Description
            </label>

            <textarea name="description"
                      rows="4"
                      class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Merchant Category
                </label>

                <input type="text"
                       name="merchant_category"
                       placeholder="e.g. School Supplies"
                       class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Maximum Amount
                </label>

                <input type="number"
                       step="0.01"
                       name="maximum_amount"
                       class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                       required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Expiration Days
                </label>

                <input type="number"
                       name="expiration_days"
                       value="30"
                       class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Status
                </label>

                <select name="status"
                        class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4">
            <button type="submit"
                    class="px-6 py-3 rounded-xl bg-teal-600 text-white font-medium hover:bg-teal-700 transition">
                Create Program
            </button>

            <a href="{{ route('admin.assistance-programs.index') }}"
               class="px-6 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 font-medium hover:bg-slate-50 transition">
                Cancel
            </a>
        </div>
    </form>

</div>

@endsection