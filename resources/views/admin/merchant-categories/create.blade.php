@extends('layouts.dashboard')

@section('title', 'Create Merchant Category')

@section('content')
<div class="max-w-4xl">
    <x-page-header
        title="Create Merchant Category"
        eyebrow="Operational Classifications"
        description="Create a reusable category for accredited merchants and assistance program validation." />

    <x-form-card
        title="Category Details"
        description="Use clear institutional categories such as School Supplies, Bookstore, Pharmacy, Groceries, or Medical Services.">
        <form method="POST" action="{{ route('admin.merchant-categories.store') }}" class="space-y-8">
            @csrf

            @include('admin.merchant-categories.partials.form', ['category' => null])
        </form>
    </x-form-card>
</div>
@endsection
