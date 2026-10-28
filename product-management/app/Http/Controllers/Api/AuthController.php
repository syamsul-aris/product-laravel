<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Log a user in and return a Sanctum token.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = $this->getAuthUser($credentials['email'], $credentials['password']);

        $token = $user->createToken('admin-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'is_admin' => $user->is_admin,
            ],
        ]);
    }

    /**
     * Log the authenticated user out and revoke the current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Return the authenticated user.
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'id'       => $request->user()->id,
                'name'     => $request->user()->name,
                'email'    => $request->user()->email,
                'is_admin' => $request->user()->is_admin,
            ],
        ]);
    }

    private function getAuthUser(string $email, string $password): object
    {
        $user = $this->getUserByEmail($email);

        if (! $user || ! $this->verifyPassword($password, $user)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user;
    }

    private function getUserByEmail(string $email): ?object
    {
        return \App\Models\User::where('email', $email)->first();
    }

    private function verifyPassword(string $plain, object $user): bool
    {
        return \Illuminate\Support\Facades\Hash::check($plain, $user->password);
    }
}
