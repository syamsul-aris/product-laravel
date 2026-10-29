@extends('admin.shared.layout')

@section('title', 'Dashboard')

@section('content')
@php
    $admin = auth('admin')->user();

    $total      = (int) ($productsCount ?? 0);
    $enabled    = (int) ($productsEnabledCount ?? 0);
    $disabled   = (int) ($productsDisabledCount ?? 0);
    $categories = (int) ($categoriesCount ?? 0);
    $withProd   = (int) ($categoriesWithProducts ?? 0);

    $enabledPct  = $total > 0 ? round($enabled / $total * 100) : 0;
    $disabledPct = $total > 0 ? 100 - $enabledPct : 0;
    $coveragePct = $categories > 0 ? round($withProd / $categories * 100) : 0;
    $emptyCats   = max($categories - $withProd, 0);

    $hour = now()->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
@endphp

<div class="space-y-8">

    <!-- Hero header -->
    <div class="relative overflow-hidden rounded-3xl border border-border bg-gradient-to-br from-primary-600 via-primary-600 to-primary-700 p-6 text-white shadow-card sm:p-8">
        <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-20 right-24 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>

        <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-widest text-white/70">{{ now()->format('l, d F Y') }}</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">
                    {{ $greeting }}{{ $admin ? ', ' . \Illuminate\Support\Str::of($admin->name)->explode(' ')->first() : '' }} 👋
                </h1>
                <p class="mt-1.5 max-w-md text-sm text-white/80">Here's a quick overview of your store today.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.products.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-primary-700 shadow-sm transition hover:bg-white/90">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Product
                </a>
                <a href="{{ route('admin.categories.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-white/30 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/20">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Category
                </a>
            </div>
        </div>
    </div>

    <!-- Stat cards -->
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <!-- Total products -->
        <a href="{{ route('admin.products.index') }}"
            class="group rounded-2xl border border-border bg-surface p-5 shadow-card transition-all hover:-translate-y-0.5 hover:border-primary-200 hover:shadow-card-hover">
            <div class="flex items-start justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-50 text-primary-600 transition-colors group-hover:bg-primary-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25-2.25M12 12.75l2.25-2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </span>
                <svg class="h-4 w-4 text-text-muted opacity-0 transition-all group-hover:translate-x-0.5 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                </svg>
            </div>
            <p class="mt-4 text-sm font-medium text-text-muted">Total products</p>
            <p class="mt-1 text-3xl font-semibold tracking-tight text-text">{{ number_format($total) }}</p>
        </a>

        <!-- Enabled -->
        <div class="rounded-2xl border border-border bg-surface p-5 shadow-card transition-all hover:-translate-y-0.5 hover:shadow-card-hover">
            <div class="flex items-start justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-success/10 text-success">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <span class="rounded-full bg-success/10 px-2.5 py-1 text-xs font-semibold text-success">{{ $enabledPct }}%</span>
            </div>
            <p class="mt-4 text-sm font-medium text-text-muted">Enabled</p>
            <p class="mt-1 text-3xl font-semibold tracking-tight text-text">{{ number_format($enabled) }}</p>
            <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-surface-2">
                <div class="h-full rounded-full bg-success transition-all" style="width: {{ $enabledPct }}%"></div>
            </div>
        </div>

        <!-- Disabled -->
        <div class="rounded-2xl border border-border bg-surface p-5 shadow-card transition-all hover:-translate-y-0.5 hover:shadow-card-hover">
            <div class="flex items-start justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-danger/10 text-danger">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </span>
                <span class="rounded-full bg-danger/10 px-2.5 py-1 text-xs font-semibold text-danger">{{ $disabledPct }}%</span>
            </div>
            <p class="mt-4 text-sm font-medium text-text-muted">Disabled</p>
            <p class="mt-1 text-3xl font-semibold tracking-tight text-text">{{ number_format($disabled) }}</p>
            <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-surface-2">
                <div class="h-full rounded-full bg-danger transition-all" style="width: {{ $disabledPct }}%"></div>
            </div>
        </div>

        <!-- Categories -->
        <a href="{{ route('admin.categories.index') }}"
            class="group rounded-2xl border border-border bg-surface p-5 shadow-card transition-all hover:-translate-y-0.5 hover:border-primary-200 hover:shadow-card-hover">
            <div class="flex items-start justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-50 text-primary-600 transition-colors group-hover:bg-primary-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                    </svg>
                </span>
                <svg class="h-4 w-4 text-text-muted opacity-0 transition-all group-hover:translate-x-0.5 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                </svg>
            </div>
            <p class="mt-4 text-sm font-medium text-text-muted">Categories</p>
            <p class="mt-1 text-3xl font-semibold tracking-tight text-text">{{ number_format($categories) }}</p>
            <p class="mt-3 text-xs text-text-muted">{{ $withProd }} with products</p>
        </a>
    </div>

    <!-- Insights row -->
    <div class="grid gap-4 lg:grid-cols-3">

        <!-- Product status donut -->
        <div class="rounded-2xl border border-border bg-surface p-6 shadow-card lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold tracking-tight text-text">Product status</h2>
                    <p class="mt-0.5 text-sm text-text-muted">Share of enabled vs disabled products</p>
                </div>
                <a href="{{ route('admin.products.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">Manage →</a>
            </div>

            <div class="mt-6 flex flex-col items-center gap-8 sm:flex-row">
                <!-- Donut -->
                <div class="relative h-40 w-40 shrink-0">
                    <div class="h-full w-full rounded-full"
                        style="background: {{ $total > 0
                            ? 'conic-gradient(rgb(34 197 94) 0 ' . $enabledPct . '%, rgb(239 68 68) ' . $enabledPct . '% 100%)'
                            : 'conic-gradient(rgb(148 163 184 / .3) 0 100%)' }};">
                    </div>
                    <div class="absolute inset-4 flex flex-col items-center justify-center rounded-full bg-surface">
                        <span class="text-2xl font-semibold tracking-tight text-text">{{ number_format($total) }}</span>
                        <span class="text-xs text-text-muted">products</span>
                    </div>
                </div>

                <!-- Legend -->
                <div class="w-full space-y-4">
                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 font-medium text-text">
                                <span class="h-2.5 w-2.5 rounded-full bg-success"></span> Enabled
                            </span>
                            <span class="text-text-muted">{{ $enabled }} · {{ $enabledPct }}%</span>
                        </div>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-surface-2">
                            <div class="h-full rounded-full bg-success" style="width: {{ $enabledPct }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 font-medium text-text">
                                <span class="h-2.5 w-2.5 rounded-full bg-danger"></span> Disabled
                            </span>
                            <span class="text-text-muted">{{ $disabled }} · {{ $disabledPct }}%</span>
                        </div>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-surface-2">
                            <div class="h-full rounded-full bg-danger" style="width: {{ $disabledPct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category coverage -->
        <div class="flex flex-col rounded-2xl border border-border bg-surface p-6 shadow-card">
            <h2 class="text-base font-semibold tracking-tight text-text">Category coverage</h2>
            <p class="mt-0.5 text-sm text-text-muted">Categories that contain products</p>

            <div class="mt-6 flex items-end gap-2">
                <span class="text-4xl font-semibold tracking-tight text-text">{{ $coveragePct }}%</span>
                <span class="pb-1 text-sm text-text-muted">{{ $withProd }} of {{ $categories }}</span>
            </div>

            <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-surface-2">
                <div class="h-full rounded-full bg-primary-600 transition-all" style="width: {{ $coveragePct }}%"></div>
            </div>

            <div class="mt-auto pt-6">
                @if ($emptyCats > 0)
                    <div class="flex items-start gap-2.5 rounded-xl bg-surface-2 px-3.5 py-3">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-text-muted" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        <p class="text-xs text-text-muted">
                            {{ $emptyCats }} {{ \Illuminate\Support\Str::plural('category', $emptyCats) }} still empty.
                            <a href="{{ route('admin.categories.index') }}" class="font-medium text-primary-600 hover:text-primary-700">Review</a>
                        </p>
                    </div>
                @else
                    <div class="flex items-start gap-2.5 rounded-xl bg-success/10 px-3.5 py-3">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-success" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <p class="text-xs font-medium text-success">Every category has products.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick actions -->
    <div>
        <h2 class="text-sm font-semibold uppercase tracking-wider text-text-muted">Quick actions</h2>

        <div class="mt-3 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $actions = [
                    ['route' => 'admin.products.create',   'title' => 'Add Product',     'desc' => 'Create a new listing',  'primary' => true,  'icon' => 'M12 4.5v15m7.5-7.5h-15'],
                    ['route' => 'admin.categories.create', 'title' => 'Add Category',    'desc' => 'Organize your catalog', 'primary' => true,  'icon' => 'M12 4.5v15m7.5-7.5h-15'],
                    ['route' => 'admin.products.index',    'title' => 'View Products',   'desc' => $total . ' total',       'primary' => false, 'icon' => 'M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z'],
                    ['route' => 'admin.categories.index',  'title' => 'View Categories', 'desc' => $categories . ' total',  'primary' => false, 'icon' => 'M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z'],
                ];
            @endphp

            @foreach ($actions as $action)
                <a href="{{ route($action['route']) }}"
                    class="group flex items-center justify-between rounded-2xl border border-border bg-surface p-4 shadow-card transition-all hover:-translate-y-0.5 hover:border-primary-200 hover:shadow-card-hover">
                    <span class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl transition-colors {{ $action['primary'] ? 'bg-primary-600 text-white shadow-sm group-hover:bg-primary-700' : 'bg-surface-2 text-text group-hover:bg-primary-50 group-hover:text-primary-600' }}">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="{{ $action['primary'] ? 2 : 1.5 }}" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['icon'] }}" />
                            </svg>
                        </span>
                        <span>
                            <span class="block text-sm font-semibold text-text">{{ $action['title'] }}</span>
                            <span class="block text-xs text-text-muted">{{ $action['desc'] }}</span>
                        </span>
                    </span>
                    <svg class="h-4 w-4 text-text-muted transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            @endforeach
        </div>
    </div>

</div>
@endsection