@extends('layouts.dashboard')

@section('title', 'Create Assistance Program')

@section('content')
    <div class="max-w-4xl">
        <x-page-header
            title="Create Assistance Program"
            eyebrow="Program Rules"
            description="Configure assistance rules for QR-based merchant claims and programmable validation." />

        <x-form-card
            title="Program Configuration"
            description="Define the program identity, merchant category rule, amount ceiling, expiration window, and availability status.">
            <form action="{{ route('admin.assistance-programs.store') }}"
                  method="POST"
                  class="space-y-8">
                @csrf

                <x-form-section
                    title="Program Details"
                    description="These details appear in admin review, member requests, and claim validation workflows.">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Program Name
                        </label>

                        <input type="text"
                               name="program_name"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                               required>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Description
                        </label>

                        <textarea name="description"
                                  rows="4"
                                  class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"></textarea>
                    </div>
                </x-form-section>

                <x-form-section
                    title="Claim Rules"
                    description="The category must match an accredited merchant category before a claim can be processed."
                    columns="2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Merchant Category
                        </label>

                        <input type="text"
                               name="merchant_category"
                               placeholder="e.g. School Supplies"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                               required>

                        <p class="mt-2 text-xs leading-5 text-ui-subtext">
                            Used by merchant validation rules.
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Maximum Amount
                        </label>

                        <input type="number"
                               step="0.01"
                               name="maximum_amount"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                               required>

                        <p class="mt-2 text-xs leading-5 text-ui-subtext">
                            Requests and approvals cannot exceed this ceiling.
                        </p>
                    </div>
                </x-form-section>

                <x-form-section
                    title="Availability"
                    description="Set how long generated claim passes remain valid and whether the program can be used."
                    columns="2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Expiration Days
                        </label>

                        <input type="number"
                               name="expiration_days"
                               value="30"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                               required>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Status
                        </label>

                        <select name="status"
                                class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </x-form-section>

                <div class="flex flex-col gap-3 border-t border-ui-border/80 pt-6 sm:flex-row sm:items-center">
                    <button type="submit"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                        Create Program
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
