@php
    use Illuminate\Support\Facades\Route;
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
<body class="bg-surface antialiased text-text font-sans">

    <!-- Top navigation -->
    <header class="flex items-center justify-between border-b border-border bg-surface/50 backdrop-blur sticky top-0 z-30">
        <div class="flex items-center gap-3 px-6 py-3">
            <a href="{{ route('admin.dashboard') }}" class="font-semibold text-base text-text tracking-tight">
                <span class="inline-flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-primary-600 text-[10px] font-bold text-white shadow-sm">P</span>
                    {{ config('app.name') }}
                </span>
            </a>
            <span class="text-border-strong">/</span>
        </div>

        <div class="flex items-center gap-3">
            @auth
                <span class="text-sm text-text-muted">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-1.5 rounded-full border border-border bg-surface-2 px-3 py-1.5 text-sm text-text-muted transition-colors hover:text-text hover:bg-surface hover:border-border-strong">
                        Sign out
                    </button>
                </form>
            @endauth
        </div>
    </header>

    <!-- Page layout -->
    <div class="min-h-screen flex">
        <!-- Sidebar accent (visual only) -->
        <aside class="w-0 lg:w-64 xl:w-72 shrink-0 border-r border-border bg-surface">
            <div class="hidden lg:block px-6 py-4">
                <p class="text-xs font-medium uppercase tracking-widest text-text-muted">Overview</p>
                <div class="mt-3 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->is('admin/dashboard') ? 'bg-primary-50 text-primary-700' : 'text-text-muted hover:text-text hover:bg-surface-2' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->is('admin/products*') ? 'bg-primary-50 text-primary-700' : 'text-text-muted hover:text-text hover:bg-surface-2' }}">
                        Products
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->is('admin/categories*') ? 'bg-primary-50 text-primary-700' : 'text-text-muted hover:text-text hover:bg-surface-2' }}">
                        Categories
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main area -->
        <main class="flex-1 lg:pl-0 xl:pl-2">
            <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

                {{-- Flash messages --}}
                @if (session('success'))
                    <div class="mb-5 rounded-xl border border-success/30 bg-success/10 px-5 py-3 text-sm font-medium text-success shadow-card">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-5 rounded-xl border border-danger/30 bg-danger/10 px-5 py-3 text-sm font-medium text-danger shadow-card">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')

            </div>
        </main>
    </div>

    {{-- Feedback toast --}}
    @if (session('success') || session('error'))
        <script>
            (function () {
                const msg = {{ Js::from(session('success') ?: session('error')) }};
                if (msg) console.info('// Flash:', msg);
            })();
        </script>
    @endif

</body>
</html>
