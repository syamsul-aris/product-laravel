@isset($category)
<div class="space-y-6">

    <!-- Header -->
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-text">Edit Category</h1>
        <p class="mt-1 text-sm text-text-muted">Update the category name below.</p>
    </div>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="rounded-2xl border border-border bg-surface p-6 shadow-card">
            <div class="space-y-1.5">
                <label for="name" class="text-sm font-medium text-text">Category name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $category->name) }}" required
                    class="block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-text shadow-sm transition-colors placeholder:text-text-muted focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                @error('name')
                    <p class="text-xs text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-3">
            <a href="{{ route('admin.categories.index') }}"
                class="inline-flex items-center gap-1.5 rounded-xl border border-border bg-surface px-4 py-2.5 text-sm font-medium text-text-muted shadow-sm transition-shadow hover:shadow-card-hover">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                Update Category
            </button>
        </div>
    </form>

    <!-- Delete -->
    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="space-y-5" onsubmit="return confirm('Delete this category?');">
        @csrf
        @method('DELETE')

        <div class="rounded-2xl border border-danger/30 bg-danger/5 p-6">
            <div class="flex items-start gap-4">
                <svg class="mt-0.5 h-6 w-6 shrink-0 text-danger" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                <div>
                    <h3 class="text-base font-semibold text-danger">Delete category</h3>
                    <p class="mt-1 text-sm text-text-muted">
                        This action cannot be undone. The category “<strong>{{ $category->name }}</strong>” will be permanently removed.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-3">
            <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-xl bg-danger px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-danger/90 focus:outline-none focus:ring-2 focus:ring-danger focus:ring-offset-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.916-2.164-2.097-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.097 1.022-2.097 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                Delete Category
            </button>
        </div>
    </form>
</div>
@else
    <div class="space-y-5">

        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-text">Create Category</h1>
            <p class="mt-1 text-sm text-text-muted">Create a new category to organize your products.</p>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="rounded-2xl border border-border bg-surface p-6 shadow-card space-y-4">
                <div class="space-y-1.5">
                    <label for="name" class="text-sm font-medium text-text">Category name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required
                        class="block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-text shadow-sm transition-colors placeholder:text-text-muted focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    @error('name')
                        <p class="text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('admin.categories.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-border bg-surface px-4 py-2.5 text-sm font-medium text-text-muted shadow-sm transition-shadow hover:shadow-card-hover">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create Category
                </button>
            </div>
        </form>
    </div>
@endisset
