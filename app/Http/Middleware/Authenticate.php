<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as MiddlewareAuthenticate;

class Authenticate extends MiddlewareAuthenticate
{

    protected function unauthenticated($request, array $guards)
    {
        if (in_array('admin', $guards)) {
            throw new AuthenticationException(redirectTo: route('admin.login'));
        }

        throw new AuthenticationException(redirectTo: route('login'));
    }
}