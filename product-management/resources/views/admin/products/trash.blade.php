@extends('admin.shared.layout')

@section('title', 'Trash')

@section('content')
<div class="space-y-6">

    <!-- Page header -->
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-semibold tracking-tight text-text">Trash</h1>
                <a href="{{ route('admin.products.index') }}"
                    class="rounded-full border border-border bg-surface px-3 py-1 text-xs font-medium text-text-muted transition-colors hover:border-border-strong hover:text-text">
                    &larr; Back to products
                </a>
            </div>
            <p class="mt-1 text-sm text-text-muted">
                {{ $products->total() }} {{ Str::plural('deleted product', $products->total()) }} — restore what you need, or delete forever.
            </p>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-2xl border border-border bg-surface shadow-card">
        @if ($products->isEmpty())
            <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                <span class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-surface-2 text-text-muted">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.916-2.164-2.097-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.097 1.022-2.097 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </span>
                <p class="text-base font-medium text-text">Trash is empty</p>
                <p class="mt-1 text-sm text-text-muted">Deleted products will appear here before you remove them permanently.</p>
                <a href="{{ route('admin.products.index') }}"
                    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">
                    Back to products
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border bg-surface-2/60">
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Product</th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Category</th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Deleted at</th>
                            <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/70">
                        @foreach ($products as $product)
                        <tr class="transition-colors hover:bg-surface-2/50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="hidden h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-surface-2 text-xs font-semibold uppercase text-text-muted sm:flex">
                                        {{ Str::of($product->name)->substr(0, 1) }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-text">{{ $product->name }}</p>
                                        <p class="text-xs text-text-muted">${{ number_format($product->price, 2) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-surface-2 px-2.5 py-1 text-xs font-medium text-text">
                                    <span class="h-1.5 w-1.5 rounded-full bg-primary-400"></span>
                                    {{ $product->category?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-text-muted">
                                {{ $product->deleted_at?->diffForHumans() ?? '—' }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm">
                                <div class="flex items-center justify-end gap-1.5">
                                    <form method="POST" action="{{ route('admin.products.restore', $product) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="rounded-lg border border-border bg-surface px-2.5 py-1.5 text-xs font-medium text-text-muted transition-colors hover:bg-surface-2 hover:text-text">
                                            Restore
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.products.force-destroy', $product) }}" class="inline" onsubmit="return confirm('Permanently delete this product? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="rounded-lg border border-transparent bg-danger/10 px-2.5 py-1.5 text-xs font-medium text-danger transition-colors hover:bg-danger/20">
                                            Delete forever
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
