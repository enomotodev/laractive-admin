<?php

namespace Enomotodev\LaractiveAdmin\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate;
use Intervention\Httpauth\Facades\Httpauth;

class HttpauthAuthenticate extends Authenticate
{
    /**
     * @param  \Illuminate\Http\Request $request
     * @param  Closure $next
     * @param  mixed ...$guards
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $config = config('laractive-admin.httpauth');

        if ($config['enable']) {
            Httpauth::secure();
        }

        return $next($request);
    }
}
