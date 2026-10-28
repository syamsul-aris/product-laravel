<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();

        if (! $user || ! $user->isAdmin()) {
            return $request->expectsJson()
                ? abort(403, 'Unauthorized action.')
                : redirect()->route('admin.login.perform');
        }

        return $next($request);
    }
}
