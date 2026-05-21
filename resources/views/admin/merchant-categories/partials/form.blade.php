<x-form-section
    title="Category"
    description="Categories are operational classifications, not individual merchants."
    columns="2">
    <div>
        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Category Name</label>
        <input id="name"
               type="text"
               name="name"
               value="{{ old('name', $category?->name) }}"
               placeholder="School Supplies"
               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
               required>
        @error('name')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
        <select id="status"
                name="status"
                class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                required>
            <option value="Active" @selected(old('status', $category?->status ?? 'Active') === 'Active')>Active</option>
            <option value="Inactive" @selected(old('status', $category?->status) === 'Inactive')>Inactive</option>
        </select>
        @error('status')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Description</label>
        <textarea id="description"
                  name="description"
                  rows="4"
                  class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">{{ old('description', $category?->description) }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>
</x-form-section>

<div class="flex flex-col gap-3 border-t border-ui-border/80 pt-6 sm:flex-row sm:items-center">
    <button type="submit"
            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
        Save Category
    </button>

    <a href="{{ route('admin.merchant-categories.index') }}"
       class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-border bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas">
        Cancel
    </a>
</div>
