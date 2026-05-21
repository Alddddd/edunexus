@extends('layouts.dashboard')

@section('title', 'Edit Assistance Program')

@section('content')
    <div class="max-w-4xl">
        <x-page-header
            title="Edit Assistance Program"
            eyebrow="Program Rules"
            description="Update assistance rules while preserving QR, claim validation, settlement, and proof flow integrity." />

        <x-form-card
            title="Program Configuration"
            description="Programs allow merchant categories. They are not directly bound to individual merchants.">
            <form action="{{ route('admin.assistance-programs.update', $assistanceProgram) }}"
                  method="POST"
                  class="space-y-8">
                @csrf
                @method('PUT')

                <x-form-section
                    title="Program Details"
                    description="These details appear in admin review, member requests, and claim validation workflows.">
                    <div>
                        <label for="program_name" class="mb-2 block text-sm font-medium text-slate-700">
                            Program Name
                        </label>

                        <input id="program_name"
                               type="text"
                               name="program_name"
                               value="{{ old('program_name', $assistanceProgram->program_name) }}"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                               required>
                        @error('program_name')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="mb-2 block text-sm font-medium text-slate-700">
                            Description
                        </label>

                        <textarea id="description"
                                  name="description"
                                  rows="4"
                                  class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">{{ old('description', $assistanceProgram->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </x-form-section>

                <x-form-section
                    title="Claim Rules"
                    description="The merchant category must be allowed by this program before a claim can be processed."
                    columns="2">
                    <div>
                        <label for="merchant_category_id" class="mb-2 block text-sm font-medium text-slate-700">
                            Allowed Merchant Category
                        </label>

                        <select id="merchant_category_id"
                                name="merchant_category_id"
                                class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                                required>
                            <option value="">Select allowed category</option>
                            @foreach($merchantCategories as $category)
                                <option value="{{ $category->id }}" @selected((string) old('merchant_category_id', $assistanceProgram->merchant_category_id) === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs leading-5 text-ui-subtext">
                            Category-based validation preserves the program to category to merchant structure.
                        </p>

                        @error('merchant_category_id')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="maximum_amount" class="mb-2 block text-sm font-medium text-slate-700">
                            Maximum Amount
                        </label>

                        <input id="maximum_amount"
                               type="number"
                               step="0.01"
                               name="maximum_amount"
                               value="{{ old('maximum_amount', $assistanceProgram->maximum_amount) }}"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                               required>

                        <p class="mt-2 text-xs leading-5 text-ui-subtext">
                            Requests and approvals cannot exceed this ceiling.
                        </p>
                        @error('maximum_amount')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </x-form-section>

                <x-form-section
                    title="Availability"
                    description="Set how long generated claim passes remain valid and whether the program can be used."
                    columns="2">
                    <div>
                        <label for="expiration_days" class="mb-2 block text-sm font-medium text-slate-700">
                            Expiration Days
                        </label>

                        <input id="expiration_days"
                               type="number"
                               name="expiration_days"
                               value="{{ old('expiration_days', $assistanceProgram->expiration_days) }}"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                               required>
                        @error('expiration_days')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="mb-2 block text-sm font-medium text-slate-700">
                            Status
                        </label>

                        <select id="status"
                                name="status"
                                class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                            <option value="Active" @selected(old('status', $assistanceProgram->status) === 'Active')>Active</option>
                            <option value="Inactive" @selected(old('status', $assistanceProgram->status) === 'Inactive')>Inactive</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </x-form-section>

                <div class="flex flex-col gap-3 border-t border-ui-border/80 pt-6 sm:flex-row sm:items-center">
                    <button type="submit"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                        Update Program
                    </button>

                    <a href="{{ route('admin.assistance-programs.index') }}"
                       class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-border bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas">
                        Cancel
                    </a>
                </div>
            </form>
        </x-form-card>
    </div>
@endsection
