<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\MerchantProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PayoutSettingsController extends Controller
{
    public function edit()
    {
        $merchantProfile = $this->merchantProfile();

        return view('merchant.payout-settings.edit', compact('merchantProfile'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'payout_account_name' => ['required', 'string', 'max:255'],
            'payout_account_number' => ['required', 'string', 'max:255'],
            'payout_qr' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'payout_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $merchantProfile = $this->merchantProfile();

        if ($request->hasFile('payout_qr')) {
            $this->deletePublicQr($merchantProfile->payout_qr);
            $validated['payout_qr'] = $request->file('payout_qr')->store('merchant-payout-qr', 'public');
        } else {
            unset($validated['payout_qr']);
        }

        $merchantProfile->update($validated);

        return redirect()
            ->route('merchant.payout-settings.edit')
            ->with('success', 'Payout settings updated successfully.');
    }

    private function merchantProfile(): MerchantProfile
    {
        return MerchantProfile::where('user_id', auth()->id())->firstOrFail();
    }

    private function deletePublicQr(?string $path): void
    {
        if (blank($path) || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        Storage::disk('public')->delete(preg_replace('#^public/#', '', $path));
    }
}
