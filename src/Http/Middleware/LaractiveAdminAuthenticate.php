<?php

namespace Enomotodev\LaractiveAdmin\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;

class LaractiveAdminAuthenticate extends Authenticate
{
    /**
     * @param  \Illuminate\Http\Request $request
     * @param  Closure $next
     * @param  mixed ...$guards
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            if ((float) str_limit(app()->version(), 3, '') >= 5.7) {
                $this->authenticate($request, ['laractive-admin']);
            } else {
                parent::authenticate(['laractive-admin']);
            }
        } catch (AuthenticationException $e) {
            return redirect()->guest(route('admin.login'));
        }

        return $next($request);
    }
}
