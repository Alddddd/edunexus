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
                <div class="mb-8 rounded-2xl border border-amber-200 bg-amber-50 p-5">
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
            @endif

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

                    <div x-data="merchantCategorySelector(@js(old('merchant_category')))" @click.outside="closeSuggestions()">
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Merchant Category
                        </label>

                        <div class="relative">
                            <input type="text"
                                   name="merchant_category"
                                   x-model="query"
                                   @focus="open = true"
                                   @click="open = true"
                                   @input="open = true"
                                   @keydown.escape="open = false"
                                   placeholder="Search or type a custom category"
                                   autocomplete="off"
                                   class="w-full rounded-xl border-slate-300 pr-11 focus:border-teal-500 focus:ring-teal-500"
                                   required>

                            <button type="button"
                                    @mousedown.prevent
                                    @click="toggleSuggestions()"
                                    class="absolute inset-y-0 right-0 flex w-11 items-center justify-center rounded-r-xl text-ui-subtext transition hover:text-ui-action"
                                    aria-label="Toggle category suggestions">
                                <x-icon name="chevron-down" size="h-4 w-4" />
                            </button>

                            <div x-cloak
                                 x-show="open && filteredCategories().length"
                                 x-transition.origin.top
                                 class="absolute z-30 mt-2 max-h-56 w-full overflow-y-auto rounded-2xl border border-ui-border bg-ui-surface p-2 shadow-xl shadow-ui-anchor/10">
                                <template x-for="category in filteredCategories()" :key="category">
                                    <button type="button"
                                            @mousedown.prevent
                                            @click="selectCategory(category)"
                                            class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-ui-text transition hover:bg-ui-canvas">
                                        <span x-text="category"></span>
                                        <x-icon name="check" size="h-4 w-4 text-ui-action" />
                                    </button>
                                </template>
                            </div>
                        </div>

                        <p class="mt-2 text-xs leading-5 text-ui-subtext">
                            Search existing assistance program categories or type a custom category.
                        </p>

                        @if($merchantCategories->isNotEmpty())
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($merchantCategories->take(6) as $category)
                                    <button type="button"
                                            @click="selectCategory(@js($category))"
                                            class="rounded-full bg-ui-canvas px-3 py-1 text-xs font-semibold text-ui-subtext ring-1 ring-ui-border transition hover:bg-ui-action hover:text-white">
                                        {{ $category }}
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        @error('merchant_category')
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
        </x-form-card>
    </div>

    <script>
        window.merchantCategorySelector = function (initialValue = '') {
            return {
                open: false,
                query: initialValue || '',
                categories: @json($merchantCategories->values()),
                filteredCategories() {
                    const search = this.query.toLowerCase().trim();

                    if (!search) {
                        return this.categories;
                    }

                    return this.categories.filter((category) =>
                        category.toLowerCase().includes(search)
                    );
                },
                selectCategory(category) {
                    this.query = category;
                    this.open = false;
                },
                toggleSuggestions() {
                    this.open = !this.open;
                },
                closeSuggestions() {
                    this.open = false;
                },
            };
        };
    </script>
@endsection
