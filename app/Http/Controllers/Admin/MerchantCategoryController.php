<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MerchantCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MerchantCategoryController extends Controller
{
    public function index()
    {
        $categories = MerchantCategory::withCount(['merchants', 'assistancePrograms'])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.merchant-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.merchant-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('merchant_categories', 'name')],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        $validated['slug'] = $this->uniqueSlug($validated['name']);

        MerchantCategory::create($validated);

        return redirect()
            ->route('admin.merchant-categories.index')
            ->with('success', 'Merchant category created successfully.');
    }

    public function edit(MerchantCategory $merchantCategory)
    {
        return view('admin.merchant-categories.edit', compact('merchantCategory'));
    }

    public function update(Request $request, MerchantCategory $merchantCategory)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('merchant_categories', 'name')->ignore($merchantCategory),
            ],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        $merchantCategory->update([
            ...$validated,
            'slug' => $this->uniqueSlug($validated['name'], $merchantCategory->id),
        ]);

        $merchantCategory->merchants()->update([
            'merchant_category' => $merchantCategory->name,
        ]);

        $merchantCategory->assistancePrograms()->update([
            'merchant_category' => $merchantCategory->name,
        ]);

        return redirect()
            ->route('admin.merchant-categories.index')
            ->with('success', 'Merchant category updated successfully.');
    }

    public function destroy(MerchantCategory $merchantCategory)
    {
        if ($merchantCategory->merchants()->exists() || $merchantCategory->assistancePrograms()->exists()) {
            return back()->with('warning', 'This category is in use and cannot be deleted.');
        }

        $merchantCategory->delete();

        return redirect()
            ->route('admin.merchant-categories.index')
            ->with('success', 'Merchant category deleted successfully.');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 2;

        while (MerchantCategory::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
