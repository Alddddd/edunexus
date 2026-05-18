@extends('layouts.dashboard')

@section('title', 'Add Merchant')

@section('content')

<div class="max-w-3xl">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800">
            Add Accredited Merchant
        </h1>

        <p class="text-slate-500 mt-2">
            Link a merchant user account to an accredited business category for claim validation.
        </p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">

        <form method="POST" action="{{ route('admin.merchants.store') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Merchant User Account
                </label>

                <select name="user_id"
                        class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                        required>
                    <option value="">Select merchant account</option>

                    @foreach($merchantUsers as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }} — {{ $user->email }}
                        </option>
                    @endforeach
                </select>

                @error('user_id')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Business Name
                </label>

                <input type="text"
                       name="business_name"
                       value="{{ old('business_name') }}"
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
                       value="{{ old('merchant_category') }}"
                       placeholder="Example: Education, Medical, Transport"
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
                       value="{{ old('contact_number') }}"
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
                          class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">{{ old('address') }}</textarea>

                @error('address')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Status
                </label>

                <select name="status"
                        class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                        required>
                    <option value="Active" selected>Active</option>
                    <option value="Inactive">Inactive</option>
                </select>

                @error('status')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                        class="px-6 py-3 rounded-xl bg-teal-600 text-white font-medium hover:bg-teal-700 transition">
                    Save Merchant
                </button>

                <a href="{{ route('admin.merchants.index') }}"
                   class="px-6 py-3 rounded-xl bg-slate-100 text-slate-700 font-medium hover:bg-slate-200 transition">
                    Cancel
                </a>
            </div>
        </form>

    </div>

</div>

@endsection