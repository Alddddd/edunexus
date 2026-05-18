@extends('layouts.dashboard')

@section('title', 'Merchants')

@section('content')

<div class="mb-8 flex items-start justify-between flex-wrap gap-4">
    <div>
        <h1 class="text-3xl font-bold text-slate-800">
            Merchant Accreditation
        </h1>

        <p class="text-slate-500 mt-2">
            Manage accredited merchants and categories used by programmable claim validation.
        </p>
    </div>

    <a href="{{ route('admin.merchants.create') }}"
       class="px-5 py-3 rounded-xl bg-teal-600 text-white font-medium hover:bg-teal-700 transition">
        Add Merchant
    </a>
</div>

@if(session('success'))
    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
        <p class="font-semibold text-emerald-800">
            {{ session('success') }}
        </p>
    </div>
@endif

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100">
        <h3 class="text-lg font-semibold text-slate-800">
            Accredited Merchant Profiles
        </h3>

        <p class="text-sm text-slate-500 mt-1">
            Merchant categories must match assistance program categories before claims can be processed.
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-4 text-left">Business</th>
                    <th class="px-6 py-4 text-left">User Account</th>
                    <th class="px-6 py-4 text-left">Category</th>
                    <th class="px-6 py-4 text-left">Status</th>
                    <th class="px-6 py-4 text-left">Contact</th>
                    <th class="px-6 py-4 text-left">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                @forelse($merchants as $merchant)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-700">
                                {{ $merchant->business_name }}
                            </p>

                            <p class="text-xs text-slate-400 mt-1">
                                {{ $merchant->address ?? 'No address provided' }}
                            </p>
                        </td>

                        <td class="px-6 py-4">
                            <p class="text-slate-700">
                                {{ $merchant->user->name }}
                            </p>

                            <p class="text-xs text-slate-400 mt-1">
                                {{ $merchant->user->email }}
                            </p>
                        </td>

                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full bg-cyan-100 text-cyan-700 text-xs font-semibold">
                                {{ $merchant->merchant_category }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $merchant->status === 'Active'
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : 'bg-slate-100 text-slate-600' }}">
                                {{ $merchant->status }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-slate-500">
                            {{ $merchant->contact_number ?? 'N/A' }}
                        </td>

                        <td class="px-6 py-4">
                            <a href="{{ route('admin.merchants.edit', $merchant) }}"
                               class="text-teal-600 font-medium hover:text-teal-700">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <p class="text-slate-400">
                                No merchant profiles yet.
                            </p>

                            <a href="{{ route('admin.merchants.create') }}"
                               class="inline-flex mt-4 px-5 py-3 rounded-xl bg-teal-600 text-white font-medium hover:bg-teal-700 transition">
                                Add First Merchant
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-100 bg-slate-50 px-6 py-4">
        <div class="flex flex-col items-center justify-center gap-3 text-center">
            <p class="text-sm text-slate-500">
                Showing {{ $merchants->firstItem() ?? 0 }} to {{ $merchants->lastItem() ?? 0 }} of {{ $merchants->total() }} merchants
            </p>

            <div class="flex justify-center">
                {{ $merchants->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
