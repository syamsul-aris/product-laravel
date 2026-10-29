<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrimStrings
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $trimmed = $request->all();

        foreach ($trimmed as $key => $value) {
            if (is_string($value)) {
                $trimmed[$key] = trim($value);
            }
        }

        $request->merge($trimmed);

        return $next($request);
    }
}
