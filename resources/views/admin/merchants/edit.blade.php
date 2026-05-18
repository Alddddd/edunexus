@extends('layouts.dashboard')

@section('title', 'Edit Merchant')

@section('content')

<div class="max-w-3xl">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800">
            Edit Merchant Profile
        </h1>

        <p class="text-slate-500 mt-2">
            Update merchant accreditation details and validation category settings.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
            <p class="font-semibold text-emerald-800">
                {{ session('success') }}
            </p>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">

        <div class="mb-6 rounded-2xl border border-slate-100 bg-slate-50 p-5">
            <p class="text-sm text-slate-500">
                Linked Merchant Account
            </p>

            <p class="font-semibold text-slate-800 mt-1">
                {{ $merchant->user->name }}
            </p>

            <p class="text-sm text-slate-500 mt-1">
                {{ $merchant->user->email }}
            </p>
        </div>

        <form method="POST"
              action="{{ route('admin.merchants.update', $merchant) }}"
              class="space-y-6">

            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Business Name
                </label>

                <input type="text"
                       name="business_name"
                       value="{{ old('business_name', $merchant->business_name) }}"
                       class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                       required>

                @error('business_name')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Merchant Category
                </label>

                <input type="text"
                       name="merchant_category"
                       value="{{ old('merchant_category', $merchant->merchant_category) }}"
                       class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                       required>

                @error('merchant_category')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Contact Number
                </label>

                <input type="text"
                       name="contact_number"
                       value="{{ old('contact_number', $merchant->contact_number) }}"
                       class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">

                @error('contact_number')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Address
                </label>

                <textarea name="address"
                          rows="3"
                          class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">{{ old('address', $merchant->address) }}</textarea>

                @error('address')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Accreditation Status
                </label>

                <select name="status"
                        class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                        required>

                    <option value="Active"
                        {{ old('status', $merchant->status) === 'Active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="Inactive"
                        {{ old('status', $merchant->status) === 'Inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>

                @error('status')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-4">

                <button type="submit"
                        class="px-6 py-3 rounded-xl bg-teal-600 text-white font-medium hover:bg-teal-700 transition">
                    Update Merchant
                </button>

                <a href="{{ route('admin.merchants.index') }}"
                   class="px-6 py-3 rounded-xl bg-slate-100 text-slate-700 font-medium hover:bg-slate-200 transition">
                    Back
                </a>

            </div>

        </form>

    </div>

</div>

@endsection