@extends('layouts.dashboard')

@section('title', 'Review Assistance Request')

@section('content')

<div class="max-w-5xl">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800">
            Review Assistance Request
        </h1>

        <p class="text-slate-500 mt-2">
            Review the member application, approve assistance, and generate a QR claim reference for merchant validation.
        </p>
    </div>

    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div>
                <p class="text-sm font-semibold text-slate-700">
                    Request Workflow Status
                </p>

                <p class="text-sm text-slate-500 mt-1">
                    Track the assistance request from submission to merchant claim processing.
                </p>
            </div>

            <span class="px-3 py-1 rounded-full text-xs font-semibold
                {{ $request->status === 'Approved'
                    ? 'bg-emerald-100 text-emerald-700'
                    : ($request->status === 'Rejected'
                        ? 'bg-red-100 text-red-700'
                        : 'bg-yellow-100 text-yellow-700') }}">
                {{ $request->status }}
            </span>
        </div>

        <div class="mt-5 flex flex-wrap items-center gap-2 text-sm">
            <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700">
                Request Submitted
            </span>

            <span>→</span>

            <span class="px-3 py-1 rounded-full {{ $request->status === 'Approved' ? 'bg-emerald-100 text-emerald-700' : ($request->status === 'Rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                {{ $request->status === 'Approved' ? 'Approved' : ($request->status === 'Rejected' ? 'Rejected' : 'Awaiting Approval') }}
            </span>

            <span>→</span>

            <span class="px-3 py-1 rounded-full {{ $request->reference_code ? 'bg-cyan-100 text-cyan-700' : 'bg-slate-100 text-slate-500' }}">
                {{ $request->reference_code ? 'QR / Reference Generated' : 'QR Pending' }}
            </span>

            <span>→</span>

            <span class="px-3 py-1 rounded-full {{ $request->is_claimed ? 'bg-teal-100 text-teal-700' : 'bg-slate-100 text-slate-500' }}">
                {{ $request->is_claimed ? 'Claimed by Merchant' : 'Not Yet Claimed' }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="p-8 border-b border-slate-100">
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">
                    Assistance Request
                </p>

                <h2 class="text-2xl font-bold text-slate-800 mt-2">
                    {{ $request->program->program_name }}
                </h2>

                <p class="text-slate-500 mt-2">
                    Submitted by {{ $request->member->name }}
                </p>
            </div>

            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-slate-100">
                <div>
                    <p class="text-sm text-slate-500">Member</p>
                    <p class="font-semibold text-slate-800 mt-1">
                        {{ $request->member->name }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Program</p>
                    <p class="font-semibold text-slate-800 mt-1">
                        {{ $request->program->program_name }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Requested Amount</p>
                    <p class="font-semibold text-slate-800 mt-1">
                        ₱{{ number_format($request->requested_amount, 2) }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Program Maximum</p>
                    <p class="font-semibold text-slate-800 mt-1">
                        PHP {{ number_format($request->program->maximum_amount, 2) }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Current Status</p>
                    <p class="font-semibold text-slate-800 mt-1">
                        {{ $request->status }}
                    </p>
                </div>
            </div>

            <div class="p-8 border-b border-slate-100">
                <p class="text-sm text-slate-500">
                    Reason / Notes
                </p>

                <p class="mt-2 text-slate-700 leading-relaxed">
                    {{ $request->reason ?? 'No reason provided.' }}
                </p>
            </div>

            @if($request->status === 'Pending')

                <div class="p-8">
                    <div class="rounded-2xl border border-yellow-200 bg-yellow-50 p-5 mb-6">
                        <p class="font-semibold text-yellow-800">
                            Pending Admin Decision
                        </p>

                        <p class="text-sm text-yellow-700 mt-1">
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

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Approved Amount
                        </label>

                        <input type="number"
                               step="0.01"
                               name="approved_amount"
                               value="{{ $request->requested_amount }}"
                               max="{{ $request->program->maximum_amount }}"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                               required>

                        <p class="mt-2 text-sm text-slate-500">
                            Program maximum: PHP {{ number_format($request->program->maximum_amount, 2) }}.
                        </p>

                        @error('approved_amount')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                        <div class="flex flex-wrap items-center gap-3 mt-6">
                            <button type="submit"
                                    class="px-6 py-3 rounded-xl bg-teal-600 text-white font-medium hover:bg-teal-700 transition disabled:opacity-70 disabled:cursor-not-allowed">
                                Approve & Generate QR
                            </button>
                        </div>
                    </form>

                    <form method="POST"
                          action="{{ route('admin.assistance-requests.reject', $request) }}"
                          class="mt-3"
                          data-confirm
                          data-confirm-title="Reject assistance request?"
                          data-confirm-message="This request will be marked as rejected and the member will be notified."
                          data-confirm-button="Reject request"
                          data-confirm-tone="danger"
                          data-loading-text="Rejecting request..."
                          data-loader-title="Rejecting request..."
                          data-loader-message="Updating the assistance request status and notifying the member.">
                        @csrf

                        <button type="submit"
                                class="px-6 py-3 rounded-xl bg-red-50 text-red-600 font-medium hover:bg-red-100 transition">
                            Reject Request
                        </button>
                    </form>
                </div>

            @else

                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <p class="text-sm text-slate-500">Reference Code</p>

                            <p class="font-mono text-sm font-semibold text-slate-800 mt-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
                                {{ $request->reference_code ?? 'Not generated' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-slate-500">Approved Amount</p>

                            <p class="font-semibold text-slate-800 mt-1">
                                ₱{{ number_format($request->approved_amount, 2) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-slate-500">Expiration Date</p>

                            <p class="font-semibold text-slate-800 mt-1">
                                {{ \Carbon\Carbon::parse($request->expiration_date)->format('M d, Y') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-slate-500">Claim Status</p>

                            <p class="font-semibold text-slate-800 mt-1">
                                {{ $request->is_claimed ? 'Claimed by Merchant' : 'Ready for Merchant Validation' }}
                            </p>
                        </div>

                    </div>
                </div>

            @endif

        </div>

        <div class="space-y-6">

            @if($request->status !== 'Pending')
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <p class="text-sm font-semibold text-slate-700">
                        QR Claim Code
                    </p>

                    <p class="text-sm text-slate-500 mt-1">
                        Member can present this code to a partner merchant for validation.
                    </p>

                    @if($request->qr_code)
                        <div class="mt-5 inline-block bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(190)->generate($request->qr_code) !!}
                        </div>

                        <p class="text-xs text-slate-400 mt-4">
                            QR payload is linked to the assistance reference.
                        </p>
                    @else
                        <div class="mt-5 rounded-2xl border border-yellow-200 bg-yellow-50 p-5">
                            <p class="font-semibold text-yellow-800">
                                QR code not generated yet
                            </p>

                            <p class="text-sm text-yellow-700 mt-1">
                                This request was approved before QR generation was added.
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            <div class="bg-teal-50 rounded-2xl border border-teal-100 p-6">
                <p class="text-sm font-semibold text-teal-800">
                    Operational Rule
                </p>

                <p class="text-sm text-teal-700 mt-2">
                    Approved assistance can only be claimed once. After merchant processing, EduNexUs records proof on Morph and creates a settlement record.
                </p>
            </div>

        </div>

    </div>

</div>

@endsection
