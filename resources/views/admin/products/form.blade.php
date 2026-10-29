<div class="space-y-6">

    <!-- Header -->
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-text">{{ isset($product) ? 'Edit Product' : 'Create Product' }}</h1>
        <p class="mt-1 text-sm text-text-muted">{{ isset($product) ? 'Update the product details below.' : 'Fill in the product details below.' }}</p>
    </div>

    <form action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST" class="space-y-5">
        @csrf
        @isset($product)
            @method('PUT')
        @endisset

        <div class="grid gap-5 sm:grid-cols-2">
            <!-- Name -->
            <div class="space-y-1.5">
                <label for="name" class="text-sm font-medium text-text">Product name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
                    class="block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-text shadow-sm transition-colors placeholder:text-text-muted focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                @error('name')
                    <p class="text-xs text-danger">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div class="space-y-1.5">
                <label for="category_id" class="text-sm font-medium text-text">Category</label>
                <select id="category_id" name="category_id" required
                    class="block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-text shadow-sm transition-colors focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    <option value="">Select a category</option>
                    @foreach ($categories as $id => $name)
                        <option value="{{ $id }}" {{ old('category_id', $product->category_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-xs text-danger">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price -->
            <div class="space-y-1.5">
                <label for="price" class="text-sm font-medium text-text">Price (RM)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-muted">RM</span>
                    <input id="price" type="number" name="price" step="0.01" min="0" value="{{ old('price', $product->price ?? '') }}" required
                        class="block w-full rounded-xl border border-border bg-surface pl-7 px-3.5 py-2.5 text-sm text-text shadow-sm transition-colors placeholder:text-text-muted focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>
                @error('price')
                    <p class="text-xs text-danger">{{ $message }}</p>
                @enderror
            </div>

            <!-- Stock -->
            <div class="space-y-1.5">
                <label for="stock" class="text-sm font-medium text-text">Stock</label>
                <input id="stock" type="number" name="stock" min="0" value="{{ old('stock', $product->stock ?? '') }}" required
                    class="block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-text shadow-sm transition-colors placeholder:text-text-muted focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                @error('stock')
                    <p class="text-xs text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Description -->
        <div class="space-y-1.5">
            <label for="description" class="text-sm font-medium text-text">Description</label>
            <textarea id="description" name="description" rows="4"
                class="block w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-text shadow-sm transition-colors placeholder:text-text-muted focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">{{ old('description', $product->description ?? '') }}</textarea>
            @error('description')
                <p class="text-xs text-danger">{{ $message }}</p>
            @enderror
        </div>

        <!-- Toggle -->
        <div class="flex items-center gap-3 rounded-xl border border-border bg-surface px-4 py-3">
            <button type="button" id="enabled-toggle"
                class="relative inline-flex h-6 w-11 shrink-0 rounded-full border border-border bg-surface-2 transition-colors" role="switch" aria-checked="{{ old('enabled', $product->enabled ?? false) ? 'true' : 'false' }}" aria-labelledby="enabled-label">
                <span class="pointer-events-none inline-block h-5 w-5 translate-x-0.5 rounded-full bg-surface shadow-sm ring-0 transition-transform {{ old('enabled', $product->enabled ?? false) ? 'translate-x-5' : '' }}"></span>
                <span class="sr-only">Toggle product enabled state</span>
            </button>
            <label id="enabled-label" class="text-sm font-medium text-text cursor-pointer" for="enabled">
                {{ old('enabled', $product->enabled ?? false) ? 'Enabled' : 'Disabled' }}
            </label>
            {{-- Hidden field first: unchecked boxes are not submitted at all,
                 so this guarantees `enabled` is always present. When checked
                 the checkbox (declared after it) wins as the last value. --}}
            <input type="hidden" name="enabled" value="0">
            <input id="enabled" type="checkbox" name="enabled" value="1"
                class="pointer-events-none absolute h-0 w-0 opacity-0"
                {{ old('enabled', $product->enabled ?? false) ? 'checked' : '' }}>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.products.index') }}"
                class="inline-flex items-center gap-1.5 rounded-xl border border-border bg-surface px-4 py-2.5 text-sm font-medium text-text-muted shadow-sm transition-shadow hover:shadow-card-hover">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                {{ isset($product) ? 'Update Product' : 'Create Product' }}
            </button>
        </div>
    </form>
</div>

<script>
    (function () {
        var toggle = document.getElementById('enabled-toggle');
        var checkbox = document.getElementById('enabled');
        var label = document.getElementById('enabled-label');

        if (!toggle || !checkbox) return;

        function sync() {
            var on = checkbox.checked;
            var knob = toggle.querySelector('span');

            toggle.setAttribute('aria-checked', on ? 'true' : 'false');
            // Swap the track color: green (success) when enabled, neutral when
            // disabled. Both classes must be managed together — two bg-*
            // utilities with equal specificity would otherwise conflict.
            toggle.classList.toggle('bg-success', on);
            toggle.classList.toggle('border-success', on);
            toggle.classList.toggle('bg-surface-2', !on);
            if (knob) knob.classList.toggle('translate-x-5', on);
            if (label) label.textContent = on ? 'Enabled' : 'Disabled';
        }

        toggle.addEventListener('click', function () {
            checkbox.checked = !checkbox.checked;
            sync();
        });

        // Keep the visual in sync when the hidden checkbox is toggled
        // through its <label for="enabled">.
        checkbox.addEventListener('change', sync);

        sync();
    })();
</script>
