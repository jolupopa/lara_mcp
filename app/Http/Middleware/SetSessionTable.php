<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Str;

class SetSessionTable
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       
        if ($request->is('admin/*')) {
            config(['session.cookie' => 'laravel_admin_session']);
             config(['session.table' => 'admin_sessions']);
            config(['session.guard' => 'admin']);
        } 
        
     

        return $next($request);
    }
}
