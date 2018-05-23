<?php

namespace Enomotodev\LaractiveAdmin\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * @return void
     */
    public function __construct() {}

    /**
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('laractive-admin::auth.login');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect(route('admin.login'));
    }

    /**
     * @return string
     */
    protected function redirectTo()
    {
        return route('admin.dashboard.index');
    }

    /**
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('laractive-admin');
    }
}
