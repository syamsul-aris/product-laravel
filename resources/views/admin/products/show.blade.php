@extends('admin.shared.layout')

@section('title', $product->name)

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-text">{{ $product->name }}</h1>
            <p class="mt-1 text-sm text-text-muted">Product #{{ $product->id }} · Created {{ $product->created_at }}</p>
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-semibold {{ $product->enabled ? 'bg-success/15 text-success' : 'bg-danger/15 text-danger' }}">
            <span class="h-2 w-2 rounded-full {{ $product->enabled ? 'bg-success' : 'bg-danger' }}"></span>
            {{ $product->enabled ? 'Enabled' : 'Disabled' }}
        </span>
    </div>

    <!-- Detail card -->
    <div class="rounded-2xl border border-border bg-surface p-6 shadow-card">
        <dl class="grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-text-muted">Category</dt>
                <dd class="mt-1 text-sm font-medium text-text">
                    @if ($product->category)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-primary-50 px-2.5 py-1 text-sm text-primary-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-primary-500"></span>
                            {{ $product->category->name }}
                        </span>
                    @else
                        <span class="text-text-muted">—</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-text-muted">Price</dt>
                <dd class="mt-1 text-sm font-semibold text-text">${{ number_format($product->price, 2) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-text-muted">Stock</dt>
                <dd class="mt-1 text-sm font-medium text-text">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full {{ $product->stock === 0 ? 'bg-danger' : 'bg-success' }}"></span>
                        {{ $product->stock }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-text-muted">Status</dt>
                <dd class="mt-1 text-sm font-medium text-text">{{ $product->enabled ? 'Enabled' : 'Disabled' }}</dd>
            </div>
        </dl>

        @if ($product->description)
            <div class="mt-5 pt-5 border-t border-border">
                <dt class="text-xs font-medium uppercase tracking-wider text-text-muted">Description</dt>
                <dd class="mt-2 text-sm leading-relaxed text-text">{{ $product->description }}</dd>
            </div>
        @endif

        <div class="mt-5 pt-5 border-t border-border grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-text-muted">Created at</dt>
                <dd class="mt-1 text-sm text-text">{{ $product->created_at }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-text-muted">Updated at</dt>
                <dd class="mt-1 text-sm text-text">{{ $product->updated_at }}</dd>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-wrap items-center justify-end gap-3">
        <a href="{{ route('admin.products.edit', $product) }}"
            class="inline-flex items-center gap-1.5 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            Edit Product
        </a>
        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline" onsubmit="return confirm('Delete this product?');">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-xl border border-transparent bg-danger/10 px-4 py-2.5 text-sm font-semibold text-danger transition-colors hover:bg-danger/20">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.916-2.164-2.097-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.097 1.022-2.097 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                Delete
            </button>
        </form>
    </div>

</div>
@endsection
