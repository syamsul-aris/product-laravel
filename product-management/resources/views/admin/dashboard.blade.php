@extends('admin.shared.layout')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Page header -->
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-text">Dashboard</h1>
        <p class="mt-1 text-sm text-text-muted">Welcome back. Here’s a quick overview of your store.</p>
    </div>

    <!-- Stat cards -->
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl border border-border bg-surface p-5 shadow-card transition-shadow">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-50 text-primary-600 shadow-sm">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25-2.25M12 12.75l2.25-2.25" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-text-muted">Total Products</p>
                    <p class="text-2xl font-semibold tracking-tight text-text">{{ $productsCount ?? 0 }}</p>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-xs text-text-muted">
                <span>{{ $productsEnabledCount ?? 0 }} enabled</span>
                <span class="text-border-strong">·</span>
                <span>{{ $productsDisabledCount ?? 0 }} disabled</span>
            </div>
        </div>

        <div class="rounded-2xl border border-border bg-surface p-5 shadow-card transition-shadow">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-50 text-primary-600 shadow-sm">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.292-5.292c.542-.827.368-1.91.33-2.607a2.25 2.25 0 01.872-2.607l9.581-9.581A2.25 2.25 0 0021 5.25V5.25a2.25 2.25 0 00-2.25 2.25zM10.5 12.75h3.375c.621 0 1.125-.504 1.125-1.125v-3.375c0-.621-.504-1.125-1.125-1.125h-3.375c-.621 0-1.125.504-1.125 1.125v3.375c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-text-muted">Categories</p>
                    <p class="text-2xl font-semibold tracking-tight text-text">{{ $categoriesCount ?? 0 }}</p>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-xs text-text-muted">
                <span>{{ $categoriesWithProducts ?? 0 }} with products</span>
            </div>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="flex flex-wrap gap-3">                    <a href="{{ route('admin.products.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add Product
        </a>
        <a href="{{ route('admin.categories.create') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-border bg-surface px-5 py-2.5 text-sm font-semibold text-text shadow-sm transition-shadow hover:shadow-card-hover">
            <svg class="h-4 w-4 text-text-muted" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.042.188-3 .512v14.25A8.985 8.985 0 016 18c2.485 0 4.788.812 6.967 2.272a8.96 8.96 0 01-2.932-.272z" />
            </svg>
            Add Category
        </a>
        <a href="{{ route('admin.products.index') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-border bg-surface px-5 py-2.5 text-sm font-semibold text-text shadow-sm transition-shadow hover:shadow-card-hover">
            <svg class="h-4 w-4 text-text-muted" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 016 20.25h2.25A2.25 2.25 0 0110.5 18v-2.25a2.25 2.25 0 01-2.25-2.25H6A2.25 2.25 0 013.75 15.75zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0120.25 10.5h-2.25A2.25 2.25 0 0115.75 8.25v2.25zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
            </svg>
            View Products
        </a>
        <a href="{{ route('admin.categories.index') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-border bg-surface px-5 py-2.5 text-sm font-semibold text-text shadow-sm transition-shadow hover:shadow-card-hover">
            <svg class="h-4 w-4 text-text-muted" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.292-5.292c.542-.827.368-1.91.33-2.607a2.25 2.25 0 01.872-2.607l9.581-9.581A2.25 2.25 0 0021 5.25V5.25a2.25 2.25 0 00-2.25 2.25zM10.5 12.75h3.375c.621 0 1.125-.504 1.125-1.125v-3.375c0-.621-.504-1.125-1.125-1.125h-3.375c-.621 0-1.125.504-1.125 1.125v3.375c0 .621.504 1.125 1.125 1.125z" />
            </svg>
            View Categories
        </a>
    </div>

</div>
@endsection
