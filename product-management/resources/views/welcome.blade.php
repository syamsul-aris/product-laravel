@php
    use Illuminate\Support\Facades\Route;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="font-inter">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in — {{ config('app.name') }}</title>
    <meta name="description" content="{{ config('app.name') }} admin sign in">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface antialiased text-text font-sans">

    <div class="relative flex min-h-screen flex-col items-center justify-center px-4 py-12">

        <!-- Brand -->
        <div class="mb-8">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-lg font-semibold tracking-tight text-text">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-600 text-sm font-bold text-white shadow-sm">P</span>
                {{ config('app.name') }}
            </a>
        </div>

        <!-- Card -->
        <div class="w-full max-w-sm rounded-2xl border border-border bg-surface shadow-card-hover transition-shadow">

            <div class="border-b border-border px-6 py-5">
                <h1 class="text-xl font-semibold tracking-tight">Sign in</h1>
                <p class="mt-1 text-sm text-text-muted">Access the admin panel to manage products and categories.</p>
            </div>

            <form method="POST" action="{{ route('admin.login.perform') }}" class="px-6 py-6">
                @csrf

                @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-danger/30 bg-danger/10 p-4">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="text-sm text-danger">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-text mb-1.5">Email address</label>
                        <input id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            class="block w-full rounded-xl border border-border bg-surface placeholder:text-text-muted text-text shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-text mb-1.5">Password</label>
                        <input id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="block w-full rounded-xl border border-border bg-surface placeholder:text-text-muted text-text shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    </div>
                </div>

                <button type="submit"
                    class="mt-6 w-full rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Sign in
                </button>
            </form>
        </div>

        <p class="mt-6 text-xs text-text-muted">Need help? Contact your administrator.</p>
    </div>

</body>
</html>
