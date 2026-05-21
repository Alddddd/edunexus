@extends('layouts.dashboard')

@section('title', 'Edit Merchant Category')

@section('content')
<div class="max-w-4xl">
    <x-page-header
        title="Edit Merchant Category"
        eyebrow="Operational Classifications"
        description="Update reusable category details while preserving existing merchant and program links." />

    <x-form-card
        title="Category Details"
        description="Renaming a category updates the legacy category label used by linked merchants and programs.">
        <form method="POST" action="{{ route('admin.merchant-categories.update', $merchantCategory) }}" class="space-y-8">
            @csrf
            @method('PUT')

            @include('admin.merchant-categories.partials.form', ['category' => $merchantCategory])
        </form>
    </x-form-card>
</div>
@endsection
