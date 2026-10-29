@extends('admin.shared.layout')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">

    <!-- Page header -->
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-text">Dashboard</h1>
        <p class="mt-1 text-sm text-text-muted">Welcome back — here’s a quick overview of your store.</p>
    </div>

    <!-- Stat cards -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

        <!-- Total products -->
        <div class="rounded-2xl border border-border bg-surface p-5 shadow-card transition-shadow hover:shadow-card-hover">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-50 text-primary-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25-2.25M12 12.75l2.25-2.25" />
                    </svg>
                </span>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-text-muted">Products</p>
                    <p class="text-2xl font-semibold tracking-tight text-text">{{ $productsCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Enabled -->
        <div class="rounded-2xl border border-border bg-surface p-5 shadow-card transition-shadow hover:shadow-card-hover">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-success/10 text-success">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-text-muted">Enabled</p>
                    <p class="text-2xl font-semibold tracking-tight text-text">{{ $productsEnabledCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Disabled -->
        <div class="rounded-2xl border border-border bg-surface p-5 shadow-card transition-shadow hover:shadow-card-hover">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-danger/10 text-danger">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </span>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-text-muted">Disabled</p>
                    <p class="text-2xl font-semibold tracking-tight text-text">{{ $productsDisabledCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="rounded-2xl border border-border bg-surface p-5 shadow-card transition-shadow hover:shadow-card-hover">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-50 text-primary-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.292-5.292c.542-.827.368-1.91.33-2.607a2.25 2.25 0 01.872-2.607l9.581-9.581A2.25 2.25 0 0021 5.25V5.25a2.25 2.25 0 00-2.25 2.25zM10.5 12.75h3.375c.621 0 1.125-.504 1.125-1.125v-3.375c0-.621-.504-1.125-1.125-1.125h-3.375c-.621 0-1.125.504-1.125 1.125v3.375c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </span>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-text-muted">Categories</p>
                    <p class="text-2xl font-semibold tracking-tight text-text">{{ $categoriesCount ?? 0 }}</p>
                </div>
            </div>
            <p class="mt-3 text-xs text-text-muted">{{ $categoriesWithProducts ?? 0 }} with products</p>
        </div>
    </div>

    <!-- Quick actions -->
    <div>
        <h2 class="text-sm font-semibold uppercase tracking-wider text-text-muted">Quick actions</h2>
        <div class="mt-3 grid gap-4 sm:grid-cols-2">
            <a href="{{ route('admin.products.create') }}"
                class="group flex items-center justify-between rounded-2xl border border-border bg-surface p-5 shadow-card transition-all hover:border-primary-200 hover:shadow-card-hover">
                <span class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-600 text-white shadow-sm">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-sm font-semibold text-text">Add Product</span>
                        <span class="block text-xs text-text-muted">Create a new product listing</span>
                    </span>
                </span>
                <svg class="h-5 w-5 text-text-muted transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </a>

            <a href="{{ route('admin.categories.create') }}"
                class="group flex items-center justify-between rounded-2xl border border-border bg-surface p-5 shadow-card transition-all hover:border-primary-200 hover:shadow-card-hover">
                <span class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-600 text-white shadow-sm">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-sm font-semibold text-text">Add Category</span>
                        <span class="block text-xs text-text-muted">Organize your catalog</span>
                    </span>
                </span>
                <svg class="h-5 w-5 text-text-muted transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </a>

            <a href="{{ route('admin.products.index') }}"
                class="group flex items-center justify-between rounded-2xl border border-border bg-surface p-5 shadow-card transition-all hover:border-primary-200 hover:shadow-card-hover">
                <span class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-surface-2 text-text">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 018.25 20.25h2.25A2.25 2.25 0 0112.75 18v-2.25a2.25 2.25 0 00-2.25-2.25H6a2.25 2.25 0 00-2.25 2.25v.75zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25A2.25 2.25 0 0113.5 8.25v2.25zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-sm font-semibold text-text">View Products</span>
                        <span class="block text-xs text-text-muted">{{ $productsCount ?? 0 }} total</span>
                    </span>
                </span>
                <svg class="h-5 w-5 text-text-muted transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </a>

            <a href="{{ route('admin.categories.index') }}"
                class="group flex items-center justify-between rounded-2xl border border-border bg-surface p-5 shadow-card transition-all hover:border-primary-200 hover:shadow-card-hover">
                <span class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-surface-2 text-text">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.292-5.292c.542-.827.368-1.91.33-2.607a2.25 2.25 0 01.872-2.607l9.581-9.581A2.25 2.25 0 0021 5.25V5.25a2.25 2.25 0 00-2.25 2.25z" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-sm font-semibold text-text">View Categories</span>
                        <span class="block text-xs text-text-muted">{{ $categoriesCount ?? 0 }} total</span>
                    </span>
                </span>
                <svg class="h-5 w-5 text-text-muted transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>
    </div>

</div>
@endsection
