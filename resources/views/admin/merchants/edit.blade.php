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

                    <div x-data="merchantCategorySelector(@js(old('merchant_category', $merchant->merchant_category)))">
                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Merchant Category
                        </label>

                        <div class="relative">
                            <input type="text"
                                   name="merchant_category"
                                   x-model="query"
                                   @focus="open = true"
                                   @input="open = true"
                                   @keydown.escape="open = false"
                                   placeholder="Search or type a custom category"
                                   autocomplete="off"
                                   class="w-full rounded-xl border-slate-300 pr-11 focus:border-teal-500 focus:ring-teal-500"
                                   required>

                            <button type="button"
                                    @click="open = !open"
                                    class="absolute inset-y-0 right-0 flex w-11 items-center justify-center rounded-r-xl text-ui-subtext transition hover:text-ui-action"
                                    aria-label="Toggle category suggestions">
                                <x-icon name="chevron-down" size="h-4 w-4" />
                            </button>

                            <div x-cloak
                                 x-show="open && filteredCategories().length"
                                 @click.away="open = false"
                                 class="absolute z-30 mt-2 max-h-56 w-full overflow-y-auto rounded-2xl border border-ui-border bg-ui-surface p-2 shadow-xl shadow-ui-anchor/10">
                                <template x-for="category in filteredCategories()" :key="category">
                                    <button type="button"
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
            };
        };
    </script>
@endsection
