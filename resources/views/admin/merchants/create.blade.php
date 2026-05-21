@extends('layouts.dashboard')

@section('title', 'Add Merchant')

@section('content')
    <div class="max-w-4xl">
        <x-page-header
            title="Add Accredited Merchant"
            eyebrow="Partner Network"
            description="Link a merchant user account to an accredited business category for claim validation." />

        <x-form-card
            title="Merchant Accreditation"
            description="Accredited merchants can validate claim passes only when their category matches the assistance program rule.">
            @if($merchantUsers->isEmpty())
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                    <p class="font-semibold text-amber-800">
                        No available merchant accounts. Create a merchant access account first.
                    </p>

                    <p class="mt-1 text-sm text-amber-700">
                        Merchant profiles can only be linked to merchant users that do not already have a profile.
                    </p>

                    <a href="{{ route('admin.merchant-users.create') }}"
                       class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                        Create Merchant Access Account
                    </a>
                </div>
            @else
            <form method="POST" action="{{ route('admin.merchants.store') }}" class="space-y-8">
                @csrf

                <x-form-section
                    title="Account Link"
                    description="Choose the platform user account that will operate this merchant profile.">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Merchant User Account
                        </label>

                        <select name="user_id"
                                class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                                required
                                @disabled($merchantUsers->isEmpty())>
                            <option value="">Select merchant account</option>

                            @foreach($merchantUsers as $user)
                                <option value="{{ $user->id }}" @selected((string) old('user_id', request('merchant_user')) === (string) $user->id)>
                                    {{ $user->name }} - {{ $user->email }}
                                </option>
                            @endforeach
                        </select>

                        @error('user_id')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </x-form-section>

                <x-form-section
                    title="Business Details"
                    description="These details appear in merchant validation, settlement monitoring, and operational review."
                    columns="2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Business Name
                        </label>

                        <input type="text"
                               name="business_name"
                               value="{{ old('business_name') }}"
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
                                <option value="{{ $category->id }}" @selected((string) old('merchant_category_id') === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs leading-5 text-ui-subtext">
                            Merchants validate claims only when this category is allowed by the selected assistance program.
                        </p>

                        @if($merchantCategories->isEmpty())
                            <a href="{{ route('admin.merchant-categories.create') }}"
                               class="mt-3 inline-flex min-h-10 items-center rounded-xl border border-teal-200 bg-teal-50 px-4 py-2 text-sm font-semibold text-teal-700">
                                Create Merchant Category
                            </a>
                        @endif

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
                               value="{{ old('payout_account_name') }}"
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
                               value="{{ old('payout_account_number') }}"
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
                               value="{{ old('payout_qr') }}"
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
                                  class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">{{ old('payout_notes') }}</textarea>

                        @error('payout_notes')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </x-form-section>

                <x-form-section
                    title="Contact and Status"
                    description="Contact details support settlement operations and admin follow-up."
                    columns="2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Contact Number
                        </label>

                        <input type="text"
                               name="contact_number"
                               value="{{ old('contact_number') }}"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">

                        @error('contact_number')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Status
                        </label>

                        <select name="status"
                                class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                                required>
                            <option value="Active" selected>Active</option>
                            <option value="Inactive">Inactive</option>
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
                                  class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">{{ old('address') }}</textarea>

                        @error('address')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </x-form-section>

                <div class="flex flex-col gap-3 border-t border-ui-border/80 pt-6 sm:flex-row sm:items-center">
                    <button type="submit"
                            @disabled($merchantUsers->isEmpty())
                            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                        Save Merchant
                    </button>

                    <a href="{{ route('admin.merchants.index') }}"
                       class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-border bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas">
                        Cancel
                    </a>
                </div>
            </form>
            @endif
        </x-form-card>
    </div>

@endsection
