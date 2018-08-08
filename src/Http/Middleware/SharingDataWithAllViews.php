<?php

namespace Enomotodev\LaractiveAdmin\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate;

class SharingDataWithAllViews extends Authenticate
{
    /**
     * @param  \Illuminate\Http\Request $request
     * @param  Closure $next
     * @param  mixed ...$guards
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        \View::share([
            'layoutView' => \Route::current()->controller::$defaultLayoutView,
            'class' => \Route::current()->controller->getClassName(),
        ]);

        return $next($request);
    }
}
