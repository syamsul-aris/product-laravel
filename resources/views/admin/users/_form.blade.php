@php
    $isEdit = $user->exists;
    $isSelf = $isSelf ?? false;
    $inputClass = 'mt-1.5 w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-text transition-colors placeholder:text-text-muted focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500';
    $errorClass = 'mt-1.5 text-xs font-medium text-danger';
@endphp

<div class="space-y-5">

    <!-- Name -->
    <div>
        <label for="name" class="block text-sm font-medium text-text">Name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus
            class="{{ $inputClass }} @error('name') border-danger @enderror">
        @error('name') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
    </div>

    <!-- Email -->
    <div>
        <label for="email" class="block text-sm font-medium text-text">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="off"
            class="{{ $inputClass }} @error('email') border-danger @enderror">
        @error('email') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
    </div>

    <!-- Password -->
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="password" class="block text-sm font-medium text-text">
                {{ $isEdit ? 'New password' : 'Password' }}
            </label>
            <input type="password" id="password" name="password" autocomplete="new-password" {{ $isEdit ? '' : 'required' }}
                class="{{ $inputClass }} @error('password') border-danger @enderror">
            @error('password')
                <p class="{{ $errorClass }}">{{ $message }}</p>
            @else
                <p class="mt-1.5 text-xs text-text-muted">
                    {{ $isEdit ? 'Leave blank to keep the current password.' : 'At least 8 characters.' }}
                </p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-text">Confirm password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                class="{{ $inputClass }}">
        </div>
    </div>

    <!-- Admin toggle -->
    <div class="rounded-xl border border-border bg-surface-2/50 p-4">
        <input type="hidden" name="is_admin" value="0">
        <label class="flex items-start gap-3 {{ $isSelf ? 'cursor-not-allowed opacity-70' : 'cursor-pointer' }}">
            <input type="checkbox" name="is_admin" value="1"
                {{ old('is_admin', $isSelf ? true : $user->is_admin) ? 'checked' : '' }}
                {{ $isSelf ? 'disabled' : '' }}
                class="mt-0.5 h-4 w-4 rounded border-border text-primary-600 focus:ring-primary-500">
            <span>
                <span class="block text-sm font-medium text-text">Administrator</span>
                <span class="block text-xs text-text-muted">
                    @if ($isSelf)
                        You can't remove your own admin role.
                    @else
                        Admins can sign in to this panel and manage products, categories and users.
                    @endif
                </span>
            </span>
        </label>
        @error('is_admin') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
    </div>
</div>