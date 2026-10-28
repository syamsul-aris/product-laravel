@extends('admin.shared.layout')

@section('title', 'Products')

@section('content')
<div class="space-y-6">

    <!-- Page header + actions -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-text">Products</h1>
            <p class="mt-1 text-sm text-text-muted">{{ $products->total() }} total</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" class="flex items-center">
                <label for="status-filter" class="text-sm text-text-muted hidden sm:inline">Filter</label>
                <select name="status"
                    id="status-filter"
                    onchange="this.form.submit()"
                    class="rounded-xl border border-border bg-surface px-3 py-2 text-sm text-text shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    <option value="">All statuses</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Enabled</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Disabled</option>
                </select>
            </form>
            <a href="{{ route('admin.products.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Product
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="rounded-2xl border border-border bg-surface shadow-card overflow-hidden">
        @if ($products->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <svg class="mb-3 h-10 w-10 text-border-strong" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25-2.25M12 12.75l2.25-2.25" />
                </svg>
                <p class="text-base font-medium text-text">No products yet</p>
                <p class="mt-1 text-sm text-text-muted">Create your first product to get started.</p>
                <a href="{{ route('admin.products.create') }}"
                    class="mt-4 inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">
                    Add Product
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-border">
                    <thead class="bg-surface-2">
                        <tr>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">
                                <input type="checkbox" id="select-all-row" class="h-4 w-4 rounded border-border text-primary-600 focus:ring-primary-500">
                            </th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Product</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Category</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Price</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Stock</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Status</th>
                            <th scope="col" class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($products as $product)
                        <tr class="transition-colors hover:bg-surface-2">
                            <td class="whitespace-nowrap px-5 py-4">
                                <input type="checkbox" name="ids[]" value="{{ $product->id }}" class="row-checkbox h-4 w-4 rounded border-border text-primary-600 focus:ring-primary-500">
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-surface-2 text-sm font-semibold text-text shadow-sm">
                                        {{ Str::of($product->name)->headline() }}
                                    </span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-text-muted">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-surface-2 px-2.5 py-1 text-xs font-medium text-text">
                                    <span class="h-1.5 w-1.5 rounded-full bg-primary-400"></span>
                                    {{ $product->category?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-text">
                                ${{ number_format($product->price, 2) }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-text-muted">
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="h-2 w-2 rounded-full {{ $product->stock === 0 ? 'bg-danger' : 'bg-success' }}"></span>
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold {{ $product->enabled ? 'bg-success/15 text-success' : 'bg-danger/15 text-danger' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $product->enabled ? 'bg-success' : 'bg-danger' }}"></span>
                                    {{ $product->enabled ? 'Enabled' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.products.show', $product) }}"
                                        class="inline-flex items-center gap-1 rounded-lg border border-border bg-surface px-2.5 py-1.5 text-xs font-medium text-text-muted transition-colors hover:bg-surface-2 hover:text-text">
                                        View
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                        class="inline-flex items-center gap-1 rounded-lg border border-border bg-surface px-2.5 py-1.5 text-xs font-medium text-text-muted transition-colors hover:bg-surface-2 hover:text-text">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline" onsubmit="return confirm('Delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 rounded-lg border border-transparent bg-danger/10 px-2.5 py-1.5 text-xs font-medium text-danger transition-colors hover:bg-danger/20">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.916-2.164-2.097-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.097 1.022-2.097 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Footer actions -->
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-border bg-surface-2 px-5 py-3">
                <form method="POST" action="{{ route('admin.products.bulk-destroy') }}" class="inline" onsubmit="return confirm('Delete the selected products?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-transparent bg-danger/10 px-3.5 py-2 text-xs font-semibold text-danger transition-colors hover:bg-danger/20 disabled:opacity-50"
                        {{ $products->where('enabled', true)->isEmpty() && $products->where('enabled', false)->isEmpty() ? 'disabled' : '' }}>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.916-2.164-2.097-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.097 1.022-2.097 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        Delete selected
                    </button>
                </form>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.products.export') }}"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-border bg-surface px-3.5 py-2 text-sm font-medium text-text-muted shadow-sm transition-shadow hover:shadow-card-hover">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Export Excel
                    </a>
                    <div class="text-sm text-text-muted">Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</div>
                </div>
            </div>

            <!-- Pagination -->
            @if ($products->hasPages())
                <div class="flex justify-center py-3">
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
    const selectAll = document.getElementById('select-all-row');
    if (!selectAll) return;

    selectAll.addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach(function (cb) {
            cb.checked = selectAll.checked;
        });
    });
})();
</script>
@endpush
