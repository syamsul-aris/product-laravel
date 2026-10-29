@extends('admin.shared.layout')

@section('title', 'Products')

@section('content')
@php
    $hasFilters = request()->hasAny(['status', 'category_id', 'q']) && (request('status') !== null && request('status') !== '' || request('category_id') || request('q'));
    $activeStatus = request('status');
@endphp

<div class="space-y-6">

    <!-- Page header -->
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-semibold tracking-tight text-text">Products</h1>
                <span class="rounded-full bg-primary-50 px-2.5 py-1 text-xs font-semibold text-primary-700">
                    {{ number_format($products->total()) }}
                </span>
            </div>
            <p class="mt-1 text-sm text-text-muted">
                Manage your catalog, stock and availability.
                @if ($activeStatus === '1') <span class="font-medium text-text">· Enabled only</span>
                @elseif ($activeStatus === '0') <span class="font-medium text-text">· Disabled only</span>
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.products.trash') }}"
                class="inline-flex items-center gap-1.5 rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm font-medium text-text-muted shadow-sm transition-colors hover:border-border-strong hover:text-text">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.916-2.164-2.097-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.097 1.022-2.097 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
                Trash
            </a>

            <a href="{{ route('admin.products.export', request()->only(['status', 'category_id', 'q'])) }}"
                class="inline-flex items-center gap-1.5 rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm font-medium text-text-muted shadow-sm transition-colors hover:border-border-strong hover:text-text">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Export
            </a>

            <a href="{{ route('admin.products.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Product
            </a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="flex flex-col gap-3 rounded-2xl border border-border bg-surface p-3 shadow-card sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-text-muted" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input type="search"
                name="q"
                value="{{ request('q') }}"
                placeholder="Search products…"
                aria-label="Search products"
                class="w-full rounded-xl border border-border bg-surface py-2.5 pl-9 pr-3 text-sm text-text transition-colors placeholder:text-text-muted focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
        </div>

        <select name="category_id"
            onchange="this.form.submit()"
            aria-label="Filter by category"
            class="rounded-xl border border-border bg-surface px-3 py-2.5 text-sm text-text transition-colors focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            <option value="">All categories</option>
            @foreach ($categories as $id => $name)
                <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>

        <select name="status"
            id="status-filter"
            onchange="this.form.submit()"
            aria-label="Filter by status"
            class="rounded-xl border border-border bg-surface px-3 py-2.5 text-sm text-text transition-colors focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            <option value="">All statuses</option>
            <option value="1" {{ $activeStatus === '1' ? 'selected' : '' }}>Enabled</option>
            <option value="0" {{ $activeStatus === '0' ? 'selected' : '' }}>Disabled</option>
        </select>

        <div class="flex items-center gap-3">
            <button type="submit"
                class="rounded-xl bg-surface-2 px-4 py-2.5 text-sm font-semibold text-text transition-colors hover:bg-primary-50 hover:text-primary-700">
                Search
            </button>

            @if ($hasFilters)
                <a href="{{ route('admin.products.index') }}" class="whitespace-nowrap text-sm font-medium text-primary-600 hover:text-primary-700">Reset</a>
            @endif
        </div>
    </form>

    <!-- Bulk form (checkboxes below are linked to it via the form="bulk-form" attribute) -->
    <form id="bulk-form" method="POST" action="{{ route('admin.products.bulk-destroy') }}"
        onsubmit="return confirm('Delete the selected products?');">
        @csrf
        @method('DELETE')
    </form>

    <!-- Table card -->
    <div class="overflow-hidden rounded-2xl border border-border bg-surface shadow-card">
        @if ($products->isEmpty())
            <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25-2.25M12 12.75l2.25-2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </span>
                @if ($hasFilters)
                    <p class="text-base font-semibold text-text">No matching products</p>
                    <p class="mt-1 max-w-sm text-sm text-text-muted">Try changing your search or filters.</p>
                    <a href="{{ route('admin.products.index') }}"
                        class="mt-5 inline-flex items-center gap-2 rounded-xl border border-border bg-surface px-4 py-2.5 text-sm font-semibold text-text shadow-sm transition-colors hover:bg-surface-2">
                        Reset filters
                    </a>
                @else
                    <p class="text-base font-semibold text-text">No products yet</p>
                    <p class="mt-1 max-w-sm text-sm text-text-muted">Create your first product to get started.</p>
                    <a href="{{ route('admin.products.create') }}"
                        class="mt-5 inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Add Product
                    </a>
                @endif
            </div>
        @else
            <!-- Bulk action bar -->
            <div id="bulk-bar" class="hidden items-center justify-between gap-3 border-b border-primary-200 bg-primary-50 px-5 py-3">
                <p class="text-sm font-medium text-primary-700">
                    <span id="selected-count">0</span> selected
                </p>
                <button type="submit" form="bulk-form" id="bulk-destroy-btn" disabled
                    class="inline-flex items-center gap-1.5 rounded-xl bg-danger px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition-opacity hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.916-2.164-2.097-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.097 1.022-2.097 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                    Delete selected
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border bg-surface-2/60">
                            <th scope="col" class="w-10 px-5 py-3 text-left">
                                <input type="checkbox" id="select-all-row" class="h-4 w-4 rounded border-border text-primary-600 focus:ring-primary-500" aria-label="Select all products">
                            </th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Product</th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Category</th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Price</th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Stock</th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Status</th>
                            <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/70">
                        @foreach ($products as $product)
                            <tr class="group transition-colors hover:bg-surface-2/50">
                                <td class="px-5 py-4">
                                    <input type="checkbox" name="ids[]" value="{{ $product->id }}" form="bulk-form"
                                        class="row-checkbox h-4 w-4 rounded border-border text-primary-600 focus:ring-primary-500"
                                        aria-label="Select {{ $product->name }}">
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-sm font-semibold uppercase text-primary-700 transition-colors group-hover:bg-primary-100 sm:flex">
                                            {{ \Illuminate\Support\Str::substr($product->name, 0, 1) }}
                                        </span>
                                        <div class="min-w-0 max-w-xs">
                                            <a href="{{ route('admin.products.show', $product) }}"
                                                class="block truncate text-sm font-semibold text-text hover:text-primary-700"
                                                title="{{ $product->name }}">
                                                {{ $product->name }}
                                            </a>
                                            <p class="truncate text-xs text-text-muted">
                                                {{ \Illuminate\Support\Str::limit($product->description ?: 'No description', 48) }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-surface-2 px-2.5 py-1 text-xs font-medium text-text">
                                        <span class="h-1.5 w-1.5 rounded-full bg-primary-400"></span>
                                        {{ $product->category?->name ?? '—' }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-text">
                                    RM{{ number_format($product->price, 2) }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm">
                                    @if ((int) $product->stock <= 0)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-danger/10 px-2.5 py-1 text-xs font-semibold text-danger">
                                            <span class="h-1.5 w-1.5 rounded-full bg-danger"></span> Out of stock
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-text-muted">
                                            <span class="h-2 w-2 rounded-full bg-success"></span>
                                            {{ number_format($product->stock) }}
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $product->enabled ? 'bg-success/15 text-success' : 'bg-danger/15 text-danger' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $product->enabled ? 'bg-success' : 'bg-danger' }}"></span>
                                        {{ $product->enabled ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.products.show', $product) }}"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-text-muted transition-colors hover:bg-surface-2 hover:text-text"
                                            title="View product" aria-label="View {{ $product->name }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>

                                        <a href="{{ route('admin.products.edit', $product) }}"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-text-muted transition-colors hover:bg-primary-50 hover:text-primary-700"
                                            title="Edit product" aria-label="Edit {{ $product->name }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </a>

                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline"
                                            onsubmit="return confirm({{ \Illuminate\Support\Js::from('Delete product "' . $product->name . '"?') }});">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-text-muted transition-colors hover:bg-danger/10 hover:text-danger"
                                                title="Delete product" aria-label="Delete {{ $product->name }}">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.916-2.164-2.097-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.097 1.022-2.097 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
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
                    Showing <span class="font-medium text-text">{{ $products->firstItem() }}</span>–<span class="font-medium text-text">{{ $products->lastItem() }}</span>
                    of <span class="font-medium text-text">{{ number_format($products->total()) }}</span>
                </p>
                <p class="text-sm text-text-muted">Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</p>
            </div>

            @if ($products->hasPages())
                <div class="flex justify-center border-t border-border px-5 py-3">
                    {{ $products->links(partial: 'components.pagination') }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    var selectAll = document.getElementById('select-all-row');
    var bulkBtn = document.getElementById('bulk-destroy-btn');
    var bulkBar = document.getElementById('bulk-bar');
    var countEl = document.getElementById('selected-count');
    if (!selectAll) return;

    var rows = Array.prototype.slice.call(document.querySelectorAll('.row-checkbox'));

    function sync() {
        var checked = rows.filter(function (cb) { return cb.checked; }).length;

        if (bulkBtn) bulkBtn.disabled = checked === 0;
        if (countEl) countEl.textContent = checked;
        if (bulkBar) {
            bulkBar.classList.toggle('hidden', checked === 0);
            bulkBar.classList.toggle('flex', checked > 0);
        }

        selectAll.checked = checked > 0 && checked === rows.length;
        selectAll.indeterminate = checked > 0 && checked < rows.length;
    }

    selectAll.addEventListener('change', function () {
        rows.forEach(function (cb) { cb.checked = selectAll.checked; });
        sync();
    });

    rows.forEach(function (cb) {
        cb.addEventListener('change', sync);
    });

    sync();
})();
</script>
@endpush