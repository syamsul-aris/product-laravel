@php
    use Illuminate\Support\Facades\Auth;
    $currentUser = Auth::guard('admin')->user();
    $activeNav = function (string $pattern) {
        return request()->is($pattern)
            ? 'bg-primary-50 text-primary-700'
            : 'text-text-muted hover:text-text hover:bg-surface-2';
    };
    $settingsOpen = request()->is('admin/categories*', 'admin/users*');
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

               <!-- Settings group -->
                <details class="group" @if ($settingsOpen) open @endif>
                    <summary
                        class="flex cursor-pointer list-none items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors hover:bg-surface-2 hover:text-text [&::-webkit-details-marker]:hidden {{ $settingsOpen ? 'text-text' : 'text-text-muted' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="flex-1">Settings</span>
                        <svg class="h-4 w-4 transition-transform group-open:rotate-90" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </summary>

                    <div class="ml-5 mt-1 space-y-1 border-l border-border pl-3">
                        <a href="{{ route('admin.categories.index') }}"
                            class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-medium transition-colors {{ $activeNav('admin/categories*') }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                            </svg>
                            Categories
                        </a>

                        <a href="{{ route('admin.users.index') }}"
                            class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-medium transition-colors {{ $activeNav('admin/users*') }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            Users
                        </a>
                    </div>
                </details>
            </nav>
        </aside>

        <!-- Main area -->
        <div class="min-w-0 flex-1">
            <!-- Mobile nav (sidebar is desktop-only) -->
            <nav class="flex items-center gap-1 overflow-x-auto border-b border-border px-4 py-2 lg:hidden">
                <a href="{{ route('admin.dashboard') }}" class="whitespace-nowrap rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ $activeNav('admin/dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.products.index') }}" class="whitespace-nowrap rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ $activeNav('admin/products*') }}">Products</a>
                <a href="{{ route('admin.categories.index') }}" class="whitespace-nowrap rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ $activeNav('admin/categories*') }}">Settings › Categories</a>
                <a href="{{ route('admin.users.index') }}" class="whitespace-nowrap rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ $activeNav('admin/users*') }}">Settings › Users</a>
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