@extends('layouts.dashboard')

@section('title', 'Validate Claim')

@section('content')

<div class="max-w-6xl space-y-6">

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-teal-700">
                    QR Validation Terminal
                </p>

                <h1 class="mt-2 text-3xl font-bold text-slate-800">
                    Merchant Claim Validation
                </h1>

                <p class="mt-2 max-w-3xl text-slate-500">
                    Scan a QR code or enter a reference code to verify programmable cooperative assistance claims.
                </p>
            </div>

            <a href="{{ route('merchant.dashboard') }}"
               class="inline-flex w-fit items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Merchant Dashboard
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
            <p class="font-semibold text-red-800">
                {{ session('error') }}
            </p>

            <p class="mt-1 text-sm text-red-700">
                Please check the reference code and try again.
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">

            <div class="border-b border-slate-100 p-8">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wider text-slate-500">
                            Secure Merchant Verification
                        </p>

                        <h2 class="mt-2 text-2xl font-bold text-slate-800">
                            Validate Member Claim Pass
                        </h2>

                        <p class="mt-2 text-slate-500">
                            EduNexUs checks claim authenticity, eligibility, expiration, and duplicate usage before settlement.
                        </p>
                    </div>

                    <span class="rounded-xl border border-teal-100 bg-teal-50 px-4 py-2 text-sm font-semibold text-teal-700">
                        Morph Ready
                    </span>
                </div>
            </div>

            <div class="p-8">

                <div class="mb-8">
                    <div id="reader"
                         class="hidden w-full overflow-hidden rounded-2xl border border-slate-200"></div>

                    <div id="scanner-fallback"
                         class="rounded-2xl border border-yellow-200 bg-yellow-50 p-5">
                        <p class="font-semibold text-yellow-800">
                            Camera scanner unavailable in this local browser session
                        </p>

                        <p class="mt-1 text-sm text-yellow-700">
                            Enter the reference code manually. QR camera scanning will work when served over HTTPS.
                        </p>
                    </div>
                </div>

                <form method="POST"
                      action="{{ route('merchant.claims.verify') }}"
                      class="space-y-6"
                      onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerText = 'Verifying claim...';">

                    @csrf

                    <div>
                        <label for="reference_code" class="mb-2 block text-sm font-semibold text-slate-700">
                            Reference Code
                        </label>

                        <input type="text"
                               id="reference_code"
                               name="reference_code"
                               placeholder="Example: EDU-20260517-XXXXXX"
                               class="w-full rounded-xl border-slate-300 font-mono text-sm focus:border-teal-500 focus:ring-teal-500"
                               required>

                        <p class="mt-2 text-xs text-slate-400">
                            Use the member's QR claim pass or manually enter the printed reference code.
                        </p>
                    </div>

                    <button type="submit"
                            class="w-full rounded-xl bg-teal-600 px-6 py-3 font-semibold text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-70">
                        Verify Claim
                    </button>

                </form>

            </div>

        </div>

        <div class="space-y-4">

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-semibold text-slate-800">
                    Validation Checks
                </p>

                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center gap-2 text-slate-700">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Approved request
                    </div>

                    <div class="flex items-center gap-2 text-slate-700">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Not expired
                    </div>

                    <div class="flex items-center gap-2 text-slate-700">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Not previously claimed
                    </div>

                    <div class="flex items-center gap-2 text-slate-700">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Amount within limit
                    </div>

                    <div class="flex items-center gap-2 text-slate-700">
                        <span class="h-2 w-2 rounded-full bg-teal-500"></span>
                        Merchant category match
                    </div>

                    <div class="flex items-center gap-2 text-slate-700">
                        <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
                        Morph proof after processing
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-teal-100 bg-teal-50 p-5">
                <p class="text-sm font-semibold text-teal-800">
                    What happens next?
                </p>

                <p class="mt-2 text-sm text-teal-700">
                    If valid, the claim can be processed by the merchant. EduNexUs will create a settlement record and record proof on Morph.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-semibold text-slate-800">
                    Terminal Reminder
                </p>

                <p class="mt-2 text-sm text-slate-500">
                    Each claim pass can only be processed once. Always confirm the member and amount before recording proof.
                </p>
            </div>

        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const reader = document.getElementById('reader');
    const fallback = document.getElementById('scanner-fallback');
    const referenceInput = document.getElementById('reference_code');

    if (!window.Html5Qrcode) {
        return;
    }

    const html5QrCode = new Html5Qrcode("reader");

    html5QrCode.start(
        {
            facingMode: "environment"
        },
        {
            fps: 10,
            qrbox: 250
        },
        (decodedText) => {
            referenceInput.value = decodedText;
            html5QrCode.stop();
        }
    ).then(() => {
        reader.classList.remove('hidden');
        fallback.classList.add('hidden');
    }).catch((err) => {
        console.log('QR Scanner unavailable:', err);

        reader.classList.add('hidden');
        fallback.classList.remove('hidden');
    });
});
</script>

@endsection
