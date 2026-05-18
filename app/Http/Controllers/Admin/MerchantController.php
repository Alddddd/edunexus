<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssistanceProgram;
use App\Models\MerchantProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MerchantController extends Controller
{
    public function index()
    {
        $merchants = MerchantProfile::with('user')
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.merchants.index', compact('merchants'));
    }

    public function create()
    {
        $merchantUsers = User::where('role', 'merchant')
            ->whereDoesntHave('merchantProfile')
            ->orderBy('name')
            ->get();

        $merchantCategories = AssistanceProgram::query()
            ->whereNotNull('merchant_category')
            ->select('merchant_category')
            ->distinct()
            ->orderBy('merchant_category')
            ->pluck('merchant_category');

        return view('admin.merchants.create', compact('merchantUsers', 'merchantCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where('role', 'merchant'),
                Rule::unique('merchant_profiles', 'user_id'),
            ],
            'business_name' => ['required', 'string', 'max:255'],
            'merchant_category' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        MerchantProfile::create($validated);

        return redirect()
            ->route('admin.merchants.index')
            ->with('success', 'Merchant profile created successfully.');
    }

    public function edit(MerchantProfile $merchant)
    {
        $merchantCategories = AssistanceProgram::query()
            ->whereNotNull('merchant_category')
            ->select('merchant_category')
            ->distinct()
            ->orderBy('merchant_category')
            ->pluck('merchant_category');

        return view('admin.merchants.edit', compact('merchant', 'merchantCategories'));
    }

    public function update(Request $request, MerchantProfile $merchant)
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'merchant_category' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        $merchant->update($validated);

        return redirect()
            ->route('admin.merchants.index')
            ->with('success', 'Merchant profile updated successfully.');
    }
}
