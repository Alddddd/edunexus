<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssistanceProgram;
use App\Models\MerchantCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssistanceProgramController extends Controller
{
    public function index()
    {
        $programs = AssistanceProgram::with('category')
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.assistance-programs.index', compact('programs'));
    }

    public function create()
    {
        $merchantCategories = MerchantCategory::where('status', 'Active')
            ->orderBy('name')
            ->get();

        return view('admin.assistance-programs.create', compact('merchantCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'merchant_category_id' => ['required', Rule::exists('merchant_categories', 'id')],
            'maximum_amount' => ['required', 'numeric', 'min:1'],
            'expiration_days' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        $category = MerchantCategory::findOrFail($validated['merchant_category_id']);
        $validated['merchant_category'] = $category->name;
        $validated['created_by'] = auth()->id();

        AssistanceProgram::create($validated);

        return redirect()
            ->route('admin.assistance-programs.index')
            ->with('success', 'Assistance program created successfully.');
    }

    public function edit(AssistanceProgram $assistanceProgram)
    {
        $assistanceProgram->loadMissing('category');

        $merchantCategories = MerchantCategory::query()
            ->where(function ($query) use ($assistanceProgram) {
                $query->where('status', 'Active');

                if ($assistanceProgram->merchant_category_id) {
                    $query->orWhere('id', $assistanceProgram->merchant_category_id);
                }
            })
            ->orderBy('name')
            ->get();

        return view('admin.assistance-programs.edit', compact('assistanceProgram', 'merchantCategories'));
    }

    public function update(Request $request, AssistanceProgram $assistanceProgram)
    {
        $validated = $request->validate([
            'program_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'merchant_category_id' => ['required', Rule::exists('merchant_categories', 'id')],
            'maximum_amount' => ['required', 'numeric', 'min:1'],
            'expiration_days' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        $category = MerchantCategory::findOrFail($validated['merchant_category_id']);
        $validated['merchant_category'] = $category->name;

        $assistanceProgram->update($validated);

        return redirect()
            ->route('admin.assistance-programs.index')
            ->with('success', 'Assistance program updated successfully.');
    }
}
