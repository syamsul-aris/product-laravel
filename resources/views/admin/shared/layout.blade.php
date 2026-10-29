@php
    use Illuminate\Support\Facades\Auth;

    $currentUser = Auth::guard('admin')->user();
    $activeNav = function (string $pattern) {
        return request()->is($pattern)
            ? 'bg-primary-50 text-primary-700'
            : 'text-text-muted hover:text-text hover:bg-surface-2';
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="font-inter">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name')) | Admin</title>
    <meta name="description" content="Product Management Admin Dashboard">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Minimal safe fallbacks until Vite builds */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }
    </style>
</head>
<body class="min-h-screen bg-surface antialiased text-text font-sans">

    <!-- Top bar -->
    <header class="sticky top-0 z-30 border-b border-border bg-surface/80 backdrop-blur">
        <div class="flex items-center justify-between gap-4 px-5 py-3 lg:px-8">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 text-sm font-semibold tracking-tight text-text">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-600 text-xs font-bold text-white shadow-sm">P</span>
                {{ config('app.name') }}
            </a>

            <div class="flex items-center gap-3">
                @if ($currentUser)
                    <span class="hidden text-sm text-text-muted sm:inline">{{ $currentUser->name }}</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-xs font-semibold uppercase text-primary-700">
                        {{ \Illuminate\Support\Str::substr($currentUser->name, 0, 1) }}
                    </span>
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="rounded-lg border border-border bg-surface px-3 py-1.5 text-sm font-medium text-text-muted transition-colors hover:border-border-strong hover:text-text">
                            Sign out
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </header>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="hidden w-60 shrink-0 border-r border-border bg-surface lg:block xl:w-64">
            <nav class="sticky top-[57px] space-y-1 px-4 py-6">
                <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-widest text-text-muted">Overview</p>

                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ $activeNav('admin/dashboard') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 018.25 20.25h2.25A2.25 2.25 0 0112.75 18v-2.25a2.25 2.25 0 00-2.25-2.25H6a2.25 2.25 0 00-2.25 2.25v.75zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25A2.25 2.25 0 0113.5 8.25v2.25zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.products.index') }}"
                    class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ $activeNav('admin/products*') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25-2.25M12 12.75l2.25-2.25" />
                    </svg>
                    Products
                </a>

                <a href="{{ route('admin.categories.index') }}"
                    class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ $activeNav('admin/categories*') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.292-5.292c.542-.827.368-1.91.33-2.607a2.25 2.25 0 01.872-2.607l9.581-9.581A2.25 2.25 0 0021 5.25V5.25a2.25 2.25 0 00-2.25 2.25z" />
                    </svg>
                    Categories
                </a>
            </nav>
        </aside>

        <!-- Main area -->
        <div class="min-w-0 flex-1">
            <!-- Mobile nav (sidebar is desktop-only) -->
            <nav class="flex items-center gap-1 overflow-x-auto border-b border-border px-4 py-2 lg:hidden">
                <a href="{{ route('admin.dashboard') }}" class="whitespace-nowrap rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ $activeNav('admin/dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.products.index') }}" class="whitespace-nowrap rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ $activeNav('admin/products*') }}">Products</a>
                <a href="{{ route('admin.categories.index') }}" class="whitespace-nowrap rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ $activeNav('admin/categories*') }}">Categories</a>
            </nav>

            <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

                @if (session('success'))
                    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-success/30 bg-success/10 px-4 py-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-success" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-medium text-success">{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-danger/30 bg-danger/10 px-4 py-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-danger" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-medium text-danger">{{ session('error') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-danger/30 bg-danger/10 px-4 py-3">
                        <p class="text-sm font-semibold text-danger">Please fix the following:</p>
                        <ul class="mt-1 list-disc space-y-0.5 pl-5 text-sm text-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')

            </main>
        </div>
    </div>

    @stack('scripts')

</body>
</html>
