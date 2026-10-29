@extends('admin.shared.layout')

@section('title', 'Users')

@section('content')
@php
    $hasFilters = filled(request('q')) || filled(request('role'));
@endphp

<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-semibold tracking-tight text-text">Users</h1>
                <span class="rounded-full bg-primary-50 px-2.5 py-1 text-xs font-semibold text-primary-700">
                    {{ number_format($users->total()) }}
                </span>
            </div>
            <p class="mt-1 text-sm text-text-muted">Manage who can access the admin panel.</p>
        </div>

        <a href="{{ route('admin.users.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add User
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" class="flex flex-col gap-3 rounded-2xl border border-border bg-surface p-3 shadow-card sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-text-muted" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search name or email…" aria-label="Search users"
                class="w-full rounded-xl border border-border bg-surface py-2.5 pl-9 pr-3 text-sm text-text transition-colors placeholder:text-text-muted focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
        </div>

        <select name="role" onchange="this.form.submit()" aria-label="Filter by role"
            class="rounded-xl border border-border bg-surface px-3 py-2.5 text-sm text-text transition-colors focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            <option value="">All roles</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admins</option>
            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Users</option>
        </select>

        <div class="flex items-center gap-3">
            <button type="submit"
                class="rounded-xl bg-surface-2 px-4 py-2.5 text-sm font-semibold text-text transition-colors hover:bg-primary-50 hover:text-primary-700">
                Search
            </button>
            @if ($hasFilters)
                <a href="{{ route('admin.users.index') }}" class="whitespace-nowrap text-sm font-medium text-primary-600 hover:text-primary-700">Reset</a>
            @endif
        </div>
    </form>

    <!-- Table card -->
    <div class="overflow-hidden rounded-2xl border border-border bg-surface shadow-card">
        @if ($users->isEmpty())
            <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </span>
                <p class="text-base font-semibold text-text">{{ $hasFilters ? 'No matching users' : 'No users yet' }}</p>
                <p class="mt-1 max-w-sm text-sm text-text-muted">
                    {{ $hasFilters ? 'Try changing your search or filters.' : 'Create the first user to get started.' }}
                </p>
                @if ($hasFilters)
                    <a href="{{ route('admin.users.index') }}"
                        class="mt-5 inline-flex items-center rounded-xl border border-border bg-surface px-4 py-2.5 text-sm font-semibold text-text shadow-sm transition-colors hover:bg-surface-2">
                        Reset filters
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border bg-surface-2/60">
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">User</th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Role</th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Created</th>
                            <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/70">
                        @foreach ($users as $user)
                            @php $isSelf = $user->id === $currentId; @endphp
                            <tr class="group transition-colors hover:bg-surface-2/50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 text-sm font-semibold uppercase text-primary-700 transition-colors group-hover:bg-primary-100">
                                            {{ \Illuminate\Support\Str::substr($user->name, 0, 1) }}
                                        </span>
                                        <div class="min-w-0 max-w-xs">
                                            <p class="flex items-center gap-2 truncate text-sm font-semibold text-text" title="{{ $user->name }}">
                                                <span class="truncate">{{ $user->name }}</span>
                                                @if ($isSelf)
                                                    <span class="rounded-full bg-primary-50 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-primary-700">You</span>
                                                @endif
                                            </p>
                                            <p class="truncate text-xs text-text-muted" title="{{ $user->email }}">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($user->is_admin)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-primary-50 px-2.5 py-1 text-xs font-semibold text-primary-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-primary-600"></span> Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-surface-2 px-2.5 py-1 text-xs font-medium text-text-muted">
                                            <span class="h-1.5 w-1.5 rounded-full bg-text-muted/50"></span> User
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-text-muted">
                                    {{ $user->created_at?->format('d M Y') ?? '—' }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-text-muted transition-colors hover:bg-primary-50 hover:text-primary-700"
                                            title="Edit user" aria-label="Edit {{ $user->name }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </a>

                                        @unless ($isSelf)
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline"
                                                onsubmit="return confirm({{ \Illuminate\Support\Js::from('Delete user "' . $user->name . '"?') }});">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-text-muted transition-colors hover:bg-danger/10 hover:text-danger"
                                                    title="Delete user" aria-label="Delete {{ $user->name }}">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.916-2.164-2.097-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.097 1.022-2.097 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-border bg-surface px-5 py-3">
                <p class="text-sm text-text-muted">
                    Showing <span class="font-medium text-text">{{ $users->firstItem() }}</span>–<span class="font-medium text-text">{{ $users->lastItem() }}</span>
                    of <span class="font-medium text-text">{{ number_format($users->total()) }}</span>
                </p>
                <p class="text-sm text-text-muted">Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</p>
            </div>

            @if ($users->hasPages())
                <div class="flex justify-center border-t border-border px-5 py-3">
                    {{ $users->links(partial: 'components.pagination') }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection