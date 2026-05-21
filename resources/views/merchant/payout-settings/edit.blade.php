@extends('layouts.dashboard')

@section('title', 'Payout Settings')

@section('content')
@php
    $payoutComplete = filled($merchantProfile->payout_account_name) && filled($merchantProfile->payout_account_number);
    $qrUrl = function (?string $path) {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $normalizedPath = preg_replace('#^public/#', '', $path);

        return \Illuminate\Support\Facades\Storage::disk('public')->exists($normalizedPath)
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($normalizedPath)
            : null;
    };
    $currentQrUrl = $qrUrl($merchantProfile->payout_qr);
@endphp

<div x-data="{ fileName: '', previewUrl: null, qrPreview: null }"
     class="w-full min-w-0 max-w-5xl space-y-6">
    <x-page-header
        title="Payout Settings"
        eyebrow="Merchant Reimbursement"
        description="Maintain the GCash destination used for simulated PHP settlement releases.">
        <x-slot:actions>
            <x-status-badge
                :status="$payoutComplete ? 'Complete' : 'Missing payout details'"
                :tone="$payoutComplete ? 'success' : 'warning'" />
        </x-slot:actions>
    </x-page-header>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
            <p class="font-semibold text-emerald-800">{{ session('success') }}</p>
            <p class="mt-1 text-sm text-emerald-700">Saved details are now current. Any local file selection has been cleared.</p>
        </div>
    @endif

    <section class="grid grid-cols-1 gap-4 lg:grid-cols-[1fr_18rem]">
        <div class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-ui-action">Current Destination</p>
                    <h2 class="mt-2 text-xl font-bold text-ui-text">GCash payout profile</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-ui-subtext">
                        These payout details identify the GCash destination reference used during settlement release review. The QR is a payout destination reference, not the payment itself.
                    </p>
                </div>

                <x-status-badge
                    :status="$payoutComplete ? 'Complete' : 'Missing payout details'"
                    :tone="$payoutComplete ? 'success' : 'warning'" />
            </div>

            <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="rounded-xl bg-ui-canvas/70 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Account Name</p>
                    <p class="mt-2 font-semibold text-ui-text">{{ $merchantProfile->payout_account_name ?: 'Not configured' }}</p>
                </div>

                <div class="rounded-xl bg-ui-canvas/70 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Mobile Number</p>
                    <p class="mt-2 font-mono font-semibold text-ui-text">{{ $merchantProfile->payout_account_number ?: 'Not configured' }}</p>
                </div>

                @if($merchantProfile->payout_notes)
                    <div class="rounded-xl bg-ui-canvas/70 p-4 sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Notes</p>
                        <p class="mt-2 text-sm leading-6 text-ui-text">{{ $merchantProfile->payout_notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="mx-auto w-full max-w-xs rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60 lg:mx-0">
            <p class="text-xs font-semibold uppercase tracking-wider text-ui-subtext">Current QR</p>
            @if($currentQrUrl)
                <button type="button"
                        @click="qrPreview = @js($currentQrUrl)"
                        class="mt-3 inline-block max-w-fit rounded-2xl border border-ui-border bg-white p-3 transition hover:border-teal-200 hover:shadow-md">
                    <img src="{{ $currentQrUrl }}"
                         alt="Current GCash payout QR"
                         class="h-44 w-44 rounded-xl object-contain">
                </button>
                <p class="mt-3 text-xs text-ui-subtext">Click the GCash destination reference to enlarge it for review.</p>
            @else
                <div class="mt-3 flex h-44 items-center justify-center rounded-2xl border border-dashed border-ui-border bg-ui-canvas/70 text-center">
                    <div>
                        <x-icon name="qr-code" size="mx-auto h-8 w-8 text-ui-subtext" />
                        <p class="mt-2 text-sm font-semibold text-ui-text">No QR uploaded</p>
                        <p class="mt-1 text-xs text-ui-subtext">QR upload is optional.</p>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <x-form-card
        title="Update Payout Details"
        description="GCash account name and mobile number are required before the cooperative can release simulated settlement payouts.">
        <form method="POST"
              action="{{ route('merchant.payout-settings.update') }}"
              enctype="multipart/form-data"
              class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="payout_account_name" class="mb-2 block text-sm font-semibold text-slate-700">GCash Account Name</label>
                    <input id="payout_account_name"
                           type="text"
                           name="payout_account_name"
                           value="{{ old('payout_account_name', $merchantProfile->payout_account_name) }}"
                           class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                           required>
                    @error('payout_account_name')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="payout_account_number" class="mb-2 block text-sm font-semibold text-slate-700">GCash Mobile Number</label>
                    <input id="payout_account_number"
                           type="text"
                           name="payout_account_number"
                           value="{{ old('payout_account_number', $merchantProfile->payout_account_number) }}"
                           class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                           required>
                    @error('payout_account_number')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="payout_qr" class="mb-2 block text-sm font-semibold text-slate-700">Optional GCash QR Image</label>
                    <label for="payout_qr"
                           class="group flex cursor-pointer flex-col gap-4 rounded-2xl border-2 border-dashed border-teal-200 bg-teal-50/60 p-5 transition hover:border-teal-300 hover:bg-teal-50 sm:flex-row sm:items-center">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white text-teal-700 shadow-sm ring-1 ring-teal-100">
                            <x-icon name="qr-code" size="h-6 w-6" />
                        </span>

                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-bold text-ui-text">Upload QR image</span>
                            <span class="mt-1 block text-sm leading-6 text-ui-subtext">Choose a JPG, PNG, or WEBP file up to 2 MB. New uploads replace the current QR after saving.</span>
                            <span x-show="fileName"
                                  x-cloak
                                  class="mt-2 block truncate rounded-lg bg-white px-3 py-2 font-mono text-xs font-semibold text-teal-700 ring-1 ring-teal-100"
                                  x-text="fileName"></span>
                        </span>

                        <span class="inline-flex min-h-10 items-center justify-center rounded-xl bg-ui-action px-4 py-2 text-xs font-semibold text-white transition group-hover:bg-ui-anchor">
                            Choose File
                        </span>
                    </label>

                    <input id="payout_qr"
                           type="file"
                           name="payout_qr"
                           accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                           class="sr-only"
                           @change="
                                const file = $event.target.files[0];
                                fileName = file ? file.name : '';
                                if (previewUrl) URL.revokeObjectURL(previewUrl);
                                previewUrl = file ? URL.createObjectURL(file) : null;
                           ">

                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <button type="button"
                                x-show="previewUrl"
                                x-cloak
                                @click="qrPreview = previewUrl"
                                class="w-fit rounded-2xl border border-teal-100 bg-white p-4 text-left transition hover:shadow-md">
                            <p class="text-xs font-semibold uppercase tracking-wider text-teal-700">Selected Preview</p>
                            <img :src="previewUrl"
                                 alt="Selected GCash payout QR preview"
                                 class="mt-3 h-36 w-36 rounded-xl border border-teal-100 bg-white object-cover">
                        </button>

                        @if($currentQrUrl)
                            <button type="button"
                                    @click="qrPreview = @js($currentQrUrl)"
                                    class="w-fit rounded-2xl border border-ui-border bg-ui-canvas/70 p-4 text-left transition hover:shadow-md">
                                <p class="text-xs font-semibold uppercase tracking-wider text-ui-subtext">Saved QR</p>
                                <img src="{{ $currentQrUrl }}"
                                     alt="Current GCash payout QR"
                                     class="mt-3 h-36 w-36 rounded-xl border border-ui-border bg-white object-cover">
                            </button>
                        @endif
                    </div>

                    @error('payout_qr')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="payout_notes" class="mb-2 block text-sm font-semibold text-slate-700">Payout Notes</label>
                    <textarea id="payout_notes"
                              name="payout_notes"
                              rows="4"
                              class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">{{ old('payout_notes', $merchantProfile->payout_notes) }}</textarea>
                    @error('payout_notes')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-ui-border/80 pt-6 sm:flex-row sm:items-center">
                <button type="submit"
                        class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                    Save Payout Settings
                </button>

                <a href="{{ route('merchant.dashboard') }}"
                   class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-border bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas">
                    Back to Dashboard
                </a>
            </div>
        </form>
    </x-form-card>

    <div x-cloak
         x-show="qrPreview"
         x-transition.opacity
         class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/70 px-4 py-6"
         @click.self="qrPreview = null">
        <div class="w-full max-w-lg rounded-3xl border border-white/10 bg-white p-4 shadow-2xl">
            <div class="flex items-center justify-between gap-3 pb-3">
                <p class="text-sm font-bold text-ui-text">GCash QR Preview</p>
                <button type="button"
                        @click="qrPreview = null"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-ui-border text-ui-subtext hover:bg-ui-canvas"
                        aria-label="Close QR preview">
                    <x-icon name="x" size="h-5 w-5" />
                </button>
            </div>
            <img :src="qrPreview"
                 alt="Expanded GCash QR preview"
                 class="max-h-[70vh] w-full rounded-2xl bg-white object-contain">
        </div>
    </div>
</div>
@endsection
