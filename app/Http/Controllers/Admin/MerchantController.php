<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MerchantCategory;
use App\Models\MerchantProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MerchantController extends Controller
{
    public function index()
    {
        $merchants = MerchantProfile::with(['user', 'category'])
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

        $merchantCategories = MerchantCategory::where('status', 'Active')
            ->orderBy('name')
            ->get();

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
            'merchant_category_id' => ['required', Rule::exists('merchant_categories', 'id')],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'payout_account_name' => ['nullable', 'string', 'max:255'],
            'payout_account_number' => ['nullable', 'string', 'max:255'],
            'payout_qr' => ['nullable', 'string', 'max:255'],
            'payout_notes' => ['nullable', 'string'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        $category = MerchantCategory::findOrFail($validated['merchant_category_id']);
        $validated['merchant_category'] = $category->name;

        MerchantProfile::create($validated);

        return redirect()
            ->route('admin.merchants.index')
            ->with('success', 'Merchant profile created successfully.');
    }

    public function edit(MerchantProfile $merchant)
    {
        $merchant->loadMissing(['user', 'category']);

        $merchantCategories = MerchantCategory::query()
            ->where(function ($query) use ($merchant) {
                $query->where('status', 'Active');

                if ($merchant->merchant_category_id) {
                    $query->orWhereKey($merchant->merchant_category_id);
                }
            })
            ->orderBy('name')
            ->get();

        return view('admin.merchants.edit', compact('merchant', 'merchantCategories'));
    }

    public function update(Request $request, MerchantProfile $merchant)
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'merchant_category_id' => ['required', Rule::exists('merchant_categories', 'id')],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'payout_account_name' => ['nullable', 'string', 'max:255'],
            'payout_account_number' => ['nullable', 'string', 'max:255'],
            'payout_qr' => ['nullable', 'string', 'max:255'],
            'payout_notes' => ['nullable', 'string'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        $category = MerchantCategory::findOrFail($validated['merchant_category_id']);
        $validated['merchant_category'] = $category->name;

        $merchant->update($validated);

        return redirect()
            ->route('admin.merchants.index')
            ->with('success', 'Merchant profile updated successfully.');
    }
}
