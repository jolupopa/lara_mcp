<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;


class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            Log::info("RedirectIfAuthenticated: Checking guard [{$guard}]. Authenticated: " . (Auth::guard($guard)->check() ? 'true' : 'false'));

            if (Auth::guard($guard)->check()) {
                if ($guard === 'admin') {
                    Log::info("RedirectIfAuthenticated: Admin authenticated, redirecting to admin.dashboard.");
                    return redirect()->route('admin.dashboard');
                }

                Log::info("RedirectIfAuthenticated: User authenticated, redirecting to dashboard.");
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
