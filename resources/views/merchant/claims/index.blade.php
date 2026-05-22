@extends('layouts.dashboard')

@section('title', 'Validate Claim')

@section('content')

<div class="w-full min-w-0 max-w-6xl space-y-6">
    <x-page-header
        title="Merchant Claim Validation"
        eyebrow="QR Validation Terminal"
        description="Scan a QR code or enter a reference code to verify programmable cooperative assistance claims.">
        <x-slot:actions>
            <a href="{{ route('merchant.dashboard') }}"
               class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-ui-border bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas sm:w-auto">
                Merchant Dashboard
            </a>
        </x-slot:actions>
    </x-page-header>

    @if(session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
            <p class="font-semibold text-rose-800">
                {{ session('error') }}
            </p>

            <p class="mt-1 text-sm text-rose-700">
                Please check the reference code and try again.
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <x-form-card
            class="xl:col-span-2"
            title="Validate Member Claim Pass"
            description="EduNexUs checks claim authenticity, eligibility, expiration, and duplicate usage before settlement.">
            <x-slot:actions>
                <x-status-badge status="Morph Ready" tone="proof" />
            </x-slot:actions>

            <div class="mb-8 space-y-4">
                <div id="scanner-launch"
                     class="rounded-2xl border border-ui-action/15 bg-gradient-to-br from-white via-ui-canvas/80 to-teal-50 p-5 text-center shadow-sm shadow-ui-anchor/5">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-ui-action/10 text-ui-action ring-1 ring-ui-action/15">
                        <x-icon name="qr-code" size="h-6 w-6" />
                    </div>

                    <p class="mt-3 font-semibold text-ui-text">
                        Camera scanner
                    </p>

                    <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-ui-subtext">
                        Camera requires HTTPS and browser permission. Manual reference entry remains available below.
                    </p>

                    <button type="button"
                            id="open-scanner"
                            class="mt-4 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-ui-action px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-ui-anchor/10 ring-1 ring-ui-action/20 transition hover:bg-ui-anchor sm:w-auto">
                        <x-icon name="camera" size="h-4 w-4" />
                        Open Camera Scanner
                    </button>
                </div>

                <div id="scanner-fallback"
                     class="hidden rounded-2xl border border-amber-200 bg-amber-50 p-4">
                    <p id="scanner-fallback-title" class="font-semibold text-amber-800">
                        Camera scanner unavailable in this local browser session
                    </p>

                    <p id="scanner-fallback-message" class="mt-1 text-sm leading-6 text-amber-700">
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

                    <p class="mt-2 text-xs leading-5 text-ui-subtext">
                        Use the member's QR claim pass or manually enter the printed reference code.
                    </p>
                </div>

                <button type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-ui-action px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor disabled:cursor-not-allowed disabled:opacity-70">
                    Verify Claim
                </button>
            </form>
        </x-form-card>

        <div class="min-w-0 space-y-4">
            <x-form-card title="Validation Checks">
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-3 text-slate-700">
                        <x-status-badge status="Approved request" tone="success" />
                    </div>

                    <div class="flex items-center gap-3 text-slate-700">
                        <x-status-badge status="Not expired" tone="success" />
                    </div>

                    <div class="flex items-center gap-3 text-slate-700">
                        <x-status-badge status="Not previously claimed" tone="success" />
                    </div>

                    <div class="flex items-center gap-3 text-slate-700">
                        <x-status-badge status="Amount within limit" tone="success" />
                    </div>

                    <div class="flex items-center gap-3 text-slate-700">
                        <x-status-badge status="Merchant category match" tone="proof" />
                    </div>

                    <div class="flex items-center gap-3 text-slate-700">
                        <x-status-badge status="Morph proof after processing" tone="proof" />
                    </div>
                </div>
            </x-form-card>

            <section class="rounded-2xl border border-teal-100 bg-teal-50 p-5">
                <p class="text-sm font-semibold text-teal-800">
                    What happens next?
                </p>

                <p class="mt-2 text-sm leading-6 text-teal-700">
                    If valid, the claim can be processed by the merchant. EduNexUs will create a settlement record and record proof on Morph.
                </p>
            </section>

            <x-form-card title="Terminal Reminder">
                <p class="text-sm leading-6 text-ui-subtext">
                    Each claim pass can only be processed once. Always confirm the member and amount before recording proof.
                </p>
            </x-form-card>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fallback = document.getElementById('scanner-fallback');
    const openScannerButton = document.getElementById('open-scanner');
    const fallbackTitle = document.getElementById('scanner-fallback-title');
    const fallbackMessage = document.getElementById('scanner-fallback-message');
    const referenceInput = document.getElementById('reference_code');
    let scannerSlot = null;
    let reader = null;
    let html5QrCode = null;
    let scannerStarted = false;

    const showFallback = (title, message) => {
        scannerSlot?.classList.add('hidden', 'scale-[0.98]', 'opacity-0');
        scannerSlot?.classList.remove('scale-100', 'opacity-100');
        fallback?.classList.remove('hidden');

        if (fallbackTitle) {
            fallbackTitle.textContent = title;
        }

        if (fallbackMessage) {
            fallbackMessage.textContent = message;
        }
    };

    const hideFallback = () => {
        fallback?.classList.add('hidden');
    };

    const showScannerSlot = () => {
        if (!scannerSlot) {
            return;
        }

        scannerSlot?.classList.remove('hidden');

        requestAnimationFrame(() => {
            scannerSlot?.classList.remove('scale-[0.98]', 'opacity-0');
            scannerSlot?.classList.add('scale-100', 'opacity-100');
        });
    };

    const createScannerSlot = () => {
        if (!scannerSlot) {
            scannerSlot = document.createElement('div');
            scannerSlot.id = 'scanner-slot';
            scannerSlot.className = 'hidden origin-top scale-[0.98] opacity-0 transition duration-200 ease-out';
            fallback?.parentNode?.insertBefore(scannerSlot, fallback);
        }

        return scannerSlot;
    };

    const createReader = () => {
        createScannerSlot();

        if (!reader) {
            reader = document.createElement('div');
            reader.id = 'reader';
            reader.className = 'min-h-[12rem] w-full overflow-hidden rounded-2xl border border-ui-border bg-ui-canvas/70 shadow-inner shadow-ui-anchor/5 sm:min-h-[16rem]';
            reader.innerHTML = '<div class="flex min-h-[12rem] items-center justify-center px-4 text-center text-sm font-semibold text-ui-subtext sm:min-h-[16rem]">Opening camera permission...</div>';
            scannerSlot.appendChild(reader);
        }

        return reader;
    };

    const scannerConfig = () => {
        const qrboxSize = Math.min(260, Math.max(180, Math.floor((reader?.clientWidth || 320) * 0.72)));

        return {
            fps: 8,
            qrbox: { width: qrboxSize, height: qrboxSize },
            aspectRatio: 1,
            disableFlip: false
        };
    };

    const extractReferenceCode = (decodedText) => {
        try {
            const payload = JSON.parse(decodedText);

            if (payload && payload.reference_code) {
                return payload.reference_code;
            }
        } catch (error) {
            // Plain reference codes remain valid.
        }

        return decodedText;
    };

    const startScanner = (cameraConfig) => {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        return html5QrCode.start(
            cameraConfig,
            scannerConfig(),
            (decodedText) => {
                referenceInput.value = extractReferenceCode(decodedText);
                referenceInput.dispatchEvent(new Event('input', { bubbles: true }));

                if (scannerStarted) {
                    html5QrCode.stop().catch(() => {});
                    scannerStarted = false;
                }
            }
        ).then(() => {
            scannerStarted = true;
            hideFallback();
            openScannerButton.disabled = false;
            openScannerButton.textContent = 'Camera Scanner Open';
        });
    };

    openScannerButton?.addEventListener('click', () => {
        hideFallback();

        if (!window.Html5Qrcode) {
            showFallback(
                'Camera scanner unavailable',
                'The QR scanner library did not load. You can still enter the claim reference manually.'
            );
            return;
        }

        const isSecureCameraContext = window.isSecureContext || ['localhost', '127.0.0.1'].includes(window.location.hostname);

        if (!isSecureCameraContext) {
            showFallback(
                'Secure connection required for camera scanning',
                'Mobile browsers require HTTPS before camera permission can initialize. Enter the reference code manually on this session.'
            );
            return;
        }

        createReader();
        showScannerSlot();
        openScannerButton.disabled = true;
        openScannerButton.textContent = 'Opening camera...';

        window.Html5Qrcode.getCameras()
            .then((devices) => {
                const backCamera = devices.find((device) => /back|rear|environment/i.test(device.label || ''));
                const cameraConfig = backCamera ? { deviceId: { exact: backCamera.id } } : { facingMode: { ideal: 'environment' } };

                return startScanner(cameraConfig);
            })
            .catch((err) => {
                console.log('QR Scanner unavailable:', err);
                openScannerButton.disabled = false;
                openScannerButton.textContent = 'Open Camera Scanner';
                reader?.remove();
                scannerSlot?.remove();
                scannerSlot = null;
                reader = null;
                html5QrCode = null;

                showFallback(
                    'Camera scanner unavailable',
                    'Camera permission was not completed or this browser blocked the scanner. Enter the reference code manually to continue validation.'
                );
            });
    });

    window.addEventListener('pagehide', () => {
        if (scannerStarted) {
            html5QrCode.stop().catch(() => {});
        }
    });
});
</script>

@endsection
