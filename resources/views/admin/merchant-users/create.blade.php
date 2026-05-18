@extends('layouts.dashboard')

@section('title', 'Create Merchant Access Account')

@section('content')
    <div class="max-w-3xl">
        <x-page-header
            title="Create Merchant Access Account"
            eyebrow="Partner Onboarding"
            description="Create login access for an accredited partner merchant before linking it to a merchant profile." />

        <x-form-card
            title="Merchant Access Account"
            description="This creates a portal login for partner merchants. The account role is locked to merchant and cannot be changed here.">
            <form method="POST" action="{{ route('admin.merchant-users.store') }}" class="space-y-8">
                @csrf

                <x-form-section columns="2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Name
                        </label>

                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                               required>

                        @error('name')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Email
                        </label>

                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                               required>

                        @error('email')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Password
                        </label>

                        <input type="password"
                               name="password"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                               required>

                        @error('password')
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
                            <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                            <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                        </select>

                        @error('status')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </x-form-section>

                <div class="rounded-2xl border border-teal-100 bg-teal-50 p-5">
                    <p class="text-sm font-semibold text-teal-800">
                        Locked role assignment
                    </p>

                    <p class="mt-1 text-sm text-teal-700">
                        This account will automatically be created with the merchant role for claim validation access only.
                    </p>
                </div>

                <div class="flex flex-col gap-3 border-t border-ui-border/80 pt-6 sm:flex-row sm:items-center">
                    <button type="submit"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                        Create Access Account
                    </button>

                    <a href="{{ route('admin.merchant-users.index') }}"
                       class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-border bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas">
                        View Access Accounts
                    </a>

                    <a href="{{ route('admin.merchants.create') }}"
                       class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-border bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas">
                        Back to Merchant Profile
                    </a>
                </div>
            </form>
        </x-form-card>
    </div>
@endsection
