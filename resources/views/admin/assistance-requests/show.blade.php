@extends('layouts.dashboard')

@section('title', 'Review Assistance Request')

@section('content')
<div class="max-w-6xl space-y-6">
    <x-page-header
        title="Review Assistance Request"
        eyebrow="Approval Workflow"
        description="Review the member application, approve assistance, and generate a QR claim reference for merchant validation.">
        <x-slot:actions>
            <x-status-badge :status="$request->status" :tone="$request->status" />
        </x-slot:actions>
    </x-page-header>

    <section class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-ui-text">
                    Request Workflow Status
                </p>

                <p class="mt-1 text-sm leading-6 text-ui-subtext">
                    Track the assistance request from submission to merchant claim processing.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-status-badge status="Request Submitted" tone="neutral" />
                <x-icon name="chevron-right" size="hidden h-4 w-4 text-slate-400 sm:block" />
                <x-status-badge
                    :status="$request->status === 'Approved' ? 'Approved' : ($request->status === 'Rejected' ? 'Rejected' : 'Awaiting Approval')"
                    :tone="$request->status === 'Approved' ? 'success' : ($request->status === 'Rejected' ? 'danger' : 'warning')" />
                <x-icon name="chevron-right" size="hidden h-4 w-4 text-slate-400 sm:block" />
                <x-status-badge
                    :status="$request->reference_code ? 'QR / Reference Generated' : 'QR Pending'"
                    :tone="$request->reference_code ? 'proof' : 'neutral'" />
                <x-icon name="chevron-right" size="hidden h-4 w-4 text-slate-400 sm:block" />
                <x-status-badge
                    :status="$request->is_claimed ? 'Claimed by Merchant' : 'Not Yet Claimed'"
                    :tone="$request->is_claimed ? 'claimed' : 'neutral'" />
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <x-form-card
            class="xl:col-span-2"
            title="Assistance Request"
            description="{{ $request->program->program_name }} submitted by {{ $request->member->name }}.">
            <div class="grid grid-cols-1 gap-4 border-b border-ui-border/80 pb-6 md:grid-cols-2">
                <div class="rounded-xl bg-ui-canvas/70 p-4">
                    <p class="text-sm text-ui-subtext">Member</p>
                    <p class="mt-1 font-semibold text-ui-text">{{ $request->member->name }}</p>
                </div>

                <div class="rounded-xl bg-ui-canvas/70 p-4">
                    <p class="text-sm text-ui-subtext">Program</p>
                    <p class="mt-1 font-semibold text-ui-text">{{ $request->program->program_name }}</p>
                </div>

                <div class="rounded-xl bg-ui-canvas/70 p-4">
                    <p class="text-sm text-ui-subtext">Requested Amount</p>
                    <p class="mt-1 text-lg font-bold text-ui-text">&#8369;{{ number_format($request->requested_amount, 2) }}</p>
                </div>

                <div class="rounded-xl bg-ui-canvas/70 p-4">
                    <p class="text-sm text-ui-subtext">Program Maximum</p>
                    <p class="mt-1 text-lg font-bold text-ui-text">&#8369;{{ number_format($request->program->maximum_amount, 2) }}</p>
                </div>

                <div class="rounded-xl bg-ui-canvas/70 p-4 md:col-span-2">
                    <p class="text-sm text-ui-subtext">Current Status</p>
                    <div class="mt-2">
                        <x-status-badge :status="$request->status" :tone="$request->status" />
                    </div>
                </div>
            </div>

            <div class="border-b border-ui-border/80 py-6">
                <p class="text-sm text-ui-subtext">Reason / Notes</p>
                <p class="mt-2 leading-relaxed text-slate-700">
                    {{ $request->reason ?? 'No reason provided.' }}
                </p>
            </div>

            @if($request->status === 'Pending')
                <div class="pt-6">
                    <div class="mb-6 max-w-2xl rounded-2xl border border-amber-200 bg-amber-50 p-5">
                        <p class="font-semibold text-amber-800">
                            Pending Admin Decision
                        </p>

                        <p class="mt-1 text-sm text-amber-700">
                            Approving this request will generate a claim reference and QR code for merchant validation.
                        </p>
                    </div>

                    <form method="POST"
                          action="{{ route('admin.assistance-requests.approve', $request) }}"
                          data-confirm
                          data-confirm-title="Approve assistance request?"
                          data-confirm-message="This will approve the request, generate the member claim reference, and create a QR claim pass."
                          data-confirm-button="Approve request"
                          data-confirm-tone="success"
                          data-loading-text="Approving request..."
                          data-loader-title="Approving assistance..."
                          data-loader-message="Generating the member claim reference and QR pass for merchant validation.">
                        @csrf

                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Approved Amount
                        </label>

                        <input type="number"
                               step="0.01"
                               name="approved_amount"
                               value="{{ $request->requested_amount }}"
                               max="{{ min($request->requested_amount, $request->program->maximum_amount) }}"
                               class="w-full max-w-sm rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                               required>

                        <p class="mt-2 text-sm text-ui-subtext">
                            Admin may approve a lower amount. Approval cannot exceed the requested amount
                            (&#8369;{{ number_format($request->requested_amount, 2) }}) or the program maximum
                            (&#8369;{{ number_format($request->program->maximum_amount, 2) }}).
                        </p>

                        @error('approved_amount')
                            <p class="mt-2 text-sm text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror

                        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <button type="submit"
                                    class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor disabled:cursor-not-allowed disabled:opacity-70">
                                Approve & Generate QR
                            </button>

                            <button type="submit"
                                    form="reject-request-form"
                                    class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-danger px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700">
                                Reject Request
                            </button>
                        </div>
                    </form>

                    <form method="POST"
                          id="reject-request-form"
                          action="{{ route('admin.assistance-requests.reject', $request) }}"
                          class="hidden"
                          data-confirm
                          data-confirm-title="Reject assistance request?"
                          data-confirm-message="This request will be marked as rejected and the member will be notified."
                          data-confirm-button="Reject request"
                          data-confirm-tone="danger"
                          data-loading-text="Rejecting request..."
                          data-loader-title="Rejecting request..."
                          data-loader-message="Updating the assistance request status and notifying the member.">
                        @csrf
                    </form>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4 pt-6 md:grid-cols-2">
                    <div class="rounded-xl bg-ui-canvas/70 p-4">
                        <p class="text-sm text-ui-subtext">Reference Code</p>
                        <p class="mt-2 break-all rounded-xl border border-ui-border bg-white px-4 py-3 font-mono text-sm font-semibold text-ui-text">
                            {{ $request->reference_code ?? 'Not generated' }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-ui-canvas/70 p-4">
                        <p class="text-sm text-ui-subtext">Approved Amount</p>
                        <p class="mt-1 text-lg font-bold text-ui-text">&#8369;{{ number_format($request->approved_amount, 2) }}</p>
                    </div>

                    <div class="rounded-xl bg-ui-canvas/70 p-4">
                        <p class="text-sm text-ui-subtext">Expiration Date</p>
                        <p class="mt-1 font-semibold text-ui-text">
                            {{ \Carbon\Carbon::parse($request->expiration_date)->format('M d, Y') }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-ui-canvas/70 p-4">
                        <p class="text-sm text-ui-subtext">Claim Status</p>
                        <div class="mt-2">
                            <x-status-badge
                                :status="$request->is_claimed ? 'Claimed by Merchant' : 'Ready for Merchant Validation'"
                                :tone="$request->is_claimed ? 'claimed' : 'proof'" />
                        </div>
                    </div>
                </div>
            @endif
        </x-form-card>

        <div class="space-y-6">
            @if($request->status !== 'Pending')
                <x-form-card
                    title="QR Claim Code"
                    description="Member can present this code to a partner merchant for validation.">
                    @if($request->qr_code)
                        <div class="mx-auto inline-block max-w-fit rounded-2xl border border-ui-border bg-white p-4 shadow-sm">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(190)->generate($request->qr_code) !!}
                        </div>

                        <p class="mt-4 text-xs text-ui-subtext">
                            QR payload is linked to the assistance reference.
                        </p>
                    @else
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                            <p class="font-semibold text-amber-800">
                                QR code not generated yet
                            </p>

                            <p class="mt-1 text-sm text-amber-700">
                                This request was approved before QR generation was added.
                            </p>
                        </div>
                    @endif
                </x-form-card>
            @endif

            <section class="rounded-2xl border border-teal-100 bg-teal-50 p-6">
                <p class="text-sm font-semibold text-teal-800">
                    Operational Rule
                </p>

                <p class="mt-2 text-sm leading-6 text-teal-700">
                    Approved assistance can only be claimed once. After merchant processing, EduNexUs records proof on Morph and creates a settlement record.
                </p>
            </section>
        </div>
    </div>
</div>
@endsection
