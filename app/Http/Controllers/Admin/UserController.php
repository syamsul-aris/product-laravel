<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $role = $request->query('role');

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(in_array($role, ['admin', 'user'], true), function ($query) use ($role) {
                $query->where('is_admin', $role === 'admin');
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'currentId' => $this->currentId(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', ['user' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::min(8)],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->is_admin = $request->boolean('is_admin');
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" created.");
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'isSelf' => $user->id === $this->currentId(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $isSelf = $user->id === $this->currentId();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        // You can never remove your own admin role
        $makeAdmin = $isSelf ? true : $request->boolean('is_admin');

        if ($user->is_admin && ! $makeAdmin && $this->adminCount() <= 1) {
            return back()->withInput()->with('error', 'You cannot remove the last admin.');
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->is_admin = $makeAdmin;

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" updated.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === $this->currentId()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->is_admin && $this->adminCount() <= 1) {
            return back()->with('error', 'You cannot delete the last admin.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$name}\" deleted.");
    }

    private function currentId(): ?int
    {
        return Auth::guard('admin')->id();
    }

    private function adminCount(): int
    {
        return User::where('is_admin', true)->count();
    }
}
