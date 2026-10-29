@extends('admin.shared.layout')

@section('title', 'Categories')

@section('content')
@php
    // Works for both paginators and plain collections
    $totalCategories = method_exists($categories, 'total') ? $categories->total() : $categories->count();
@endphp

<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-semibold tracking-tight text-text">Categories</h1>
                <span class="rounded-full bg-primary-50 px-2.5 py-1 text-xs font-semibold text-primary-700">
                    {{ number_format($totalCategories) }}
                </span>
            </div>
            <p class="mt-1 text-sm text-text-muted">Organize your products into groups.</p>
        </div>

        <a href="{{ route('admin.categories.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add Category
        </a>
    </div>

    @if ($categories->isEmpty())
        <!-- Empty state -->
        <div class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-border bg-surface px-6 py-16 text-center">
            <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                </svg>
            </span>
            <p class="text-base font-semibold text-text">No categories yet</p>
            <p class="mt-1 max-w-sm text-sm text-text-muted">Create your first category to start organizing your products.</p>
            <a href="{{ route('admin.categories.create') }}"
                class="mt-5 inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Category
            </a>
        </div>
    @else
        <!-- Grid -->
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($categories as $category)
                @php
                    // Uses withCount('products') when the controller provides it, otherwise falls back to a query
                    $productCount = $category->products_count ?? $category->products()->count();
                @endphp

                <div class="group relative flex flex-col rounded-2xl border border-border bg-surface p-5 shadow-card transition-all hover:-translate-y-0.5 hover:border-primary-200 hover:shadow-card-hover">

                    <div class="flex items-start justify-between gap-3">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600 transition-colors group-hover:bg-primary-100">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-text" title="{{ $category->name }}">{{ $category->name }}</p>
                                <p class="mt-0.5 text-xs text-text-muted">
                                    {{ number_format($productCount) }} {{ \Illuminate\Support\Str::plural('product', $productCount) }}
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex shrink-0 items-center gap-1">
                            <a href="{{ route('admin.categories.edit', $category) }}"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-text-muted transition-colors hover:bg-primary-50 hover:text-primary-700"
                                title="Edit category" aria-label="Edit {{ $category->name }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </a>

                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline"
                                onsubmit="return confirm({{ \Illuminate\Support\Js::from('Delete category "' . $category->name . '"?') }});">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-text-muted transition-colors hover:bg-danger/10 hover:text-danger"
                                    title="Delete category" aria-label="Delete {{ $category->name }}">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.916-2.164-2.097-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.097 1.022-2.097 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-4 flex items-center justify-between border-t border-border pt-3">
                        @if ($productCount > 0)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-success/10 px-2.5 py-1 text-xs font-medium text-success">
                                <span class="h-1.5 w-1.5 rounded-full bg-success"></span> In use
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-surface-2 px-2.5 py-1 text-xs font-medium text-text-muted">
                                <span class="h-1.5 w-1.5 rounded-full bg-text-muted/50"></span> Empty
                            </span>
                        @endif

                        <a href="{{ route('admin.categories.edit', $category) }}"
                            class="text-xs font-medium text-primary-600 transition-colors hover:text-primary-700">
                            Edit →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if ($categories->hasPages())
            <div class="flex justify-center py-3">
                {{ $categories->links(partial: 'components.pagination') }}
            </div>
        @endif
    @endif

</div>
@endsection