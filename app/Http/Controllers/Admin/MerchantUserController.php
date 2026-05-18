<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MerchantUserController extends Controller
{
    public function index()
    {
        $merchantUsers = User::with('merchantProfile')
            ->where('role', 'merchant')
            ->orderBy('name')
            ->paginate(5)
            ->withQueryString();

        return view('admin.merchant-users.index', compact('merchantUsers'));
    }

    public function create()
    {
        return view('admin.merchant-users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'merchant',
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.merchants.create', ['merchant_user' => $user->id])
            ->with('success', 'Merchant access account created. Complete the merchant profile to enable claim validation.');
    }
}
