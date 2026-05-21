@extends('layouts.dashboard')

@section('title', 'Edit Merchant')

@section('content')
    <div class="max-w-4xl">
        <x-page-header
            title="Edit Merchant Profile"
            eyebrow="Partner Network"
            description="Update merchant accreditation details and validation category settings." />

        @if(session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                <p class="font-semibold text-emerald-800">
                    {{ session('success') }}
                </p>
            </div>
        @endif

        <x-form-card
            title="Merchant Accreditation Details"
            description="Keep business, category, contact, and accreditation status aligned with claim validation operations.">
            <div class="mb-8 rounded-2xl border border-ui-border bg-ui-canvas/70 p-5">
                <p class="text-sm text-ui-subtext">
                    Linked Merchant Account
                </p>

                <p class="mt-1 font-semibold text-ui-text">
                    {{ $merchant->user->name }}
                </p>

                <p class="mt-1 text-sm text-ui-subtext">
                    {{ $merchant->user->email }}
                </p>
            </div>

            <form method="POST"
                  action="{{ route('admin.merchants.update', $merchant) }}"
                  class="space-y-8">
                @csrf
                @method('PUT')

                <x-form-section
                    title="Business Details"
                    description="These fields control how the merchant appears in validation and settlement workflows."
                    columns="2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Business Name
                        </label>

                        <input type="text"
                               name="business_name"
                               value="{{ old('business_name', $merchant->business_name) }}"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                               required>

                        @error('business_name')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Merchant Category
                        </label>

                        <select name="merchant_category_id"
                                class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                                required>
                            <option value="">Select merchant category</option>
                            @foreach($merchantCategories as $category)
                                <option value="{{ $category->id }}" @selected((string) old('merchant_category_id', $merchant->merchant_category_id) === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs leading-5 text-ui-subtext">
                            Merchants validate claims only when this category is allowed by the selected assistance program.
                        </p>

                        @error('merchant_category_id')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </x-form-section>

                <x-form-section
                    title="GCash Payout Details"
                    description="Used by admins when simulating PHP reimbursement releases during settlement review."
                    columns="2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            GCash Name
                        </label>

                        <input type="text"
                               name="payout_account_name"
                               value="{{ old('payout_account_name', $merchant->payout_account_name) }}"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">

                        @error('payout_account_name')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            GCash Number
                        </label>

                        <input type="text"
                               name="payout_account_number"
                               value="{{ old('payout_account_number', $merchant->payout_account_number) }}"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">

                        @error('payout_account_number')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Optional QR Reference
                        </label>

                        <input type="text"
                               name="payout_qr"
                               value="{{ old('payout_qr', $merchant->payout_qr) }}"
                               placeholder="QR image URL or internal reference"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">

                        @error('payout_qr')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Payout Notes
                        </label>

                        <textarea name="payout_notes"
                                  rows="3"
                                  class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">{{ old('payout_notes', $merchant->payout_notes) }}</textarea>

                        @error('payout_notes')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </x-form-section>

                <x-form-section
                    title="Contact and Accreditation"
                    description="Use these details for settlement coordination and merchant eligibility."
                    columns="2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Contact Number
                        </label>

                        <input type="text"
                               name="contact_number"
                               value="{{ old('contact_number', $merchant->contact_number) }}"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">

                        @error('contact_number')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
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
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Address
                        </label>

                        <textarea name="address"
                                  rows="3"
                                  class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">{{ old('address', $merchant->address) }}</textarea>

                        @error('address')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </x-form-section>

                <div class="flex flex-col gap-3 border-t border-ui-border/80 pt-6 sm:flex-row sm:items-center">
                    <button type="submit"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                        Update Merchant
                    </button>

                    <a href="{{ route('admin.merchants.index') }}"
                       class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-border bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas">
                        Back
                    </a>
                </div>
            </form>
        </x-form-card>
    </div>

@endsection
