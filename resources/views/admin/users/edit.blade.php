@extends('admin.shared.layout')

@section('title', 'Edit User')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">

    <div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-text-muted transition-colors hover:text-text">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
            Back to users
        </a>
        <h1 class="mt-3 text-2xl font-semibold tracking-tight text-text">Edit User</h1>
        <p class="mt-1 text-sm text-text-muted">Update details for {{ $user->name }}.</p>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="rounded-2xl border border-border bg-surface p-6 shadow-card">
        @csrf
        @method('PUT')

        @include('admin.users._form', ['user' => $user, 'isSelf' => $isSelf])

        <div class="mt-6 flex items-center justify-end gap-3 border-t border-border pt-5">
            <a href="{{ route('admin.users.index') }}"
                class="rounded-xl border border-border bg-surface px-4 py-2.5 text-sm font-medium text-text-muted transition-colors hover:bg-surface-2 hover:text-text">
                Cancel
            </a>
            <button type="submit"
                class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection