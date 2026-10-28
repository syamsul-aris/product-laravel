<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $guard = null): Response
    {
        if ($guard === 'admin') {
            $auth = Auth::guard('admin');

            if ($auth->check() && ! $request->routeIs('admin.login.perform')) {
                return redirect()->route('admin.login.perform');
            }
        } else {
            $auth = Auth::guard('web');

            if ($auth->check() && ! $request->routeIs('dashboard')) {
                return redirect()->route('admin.login.perform');
            }
        }

        return $next($request);
    }
}
