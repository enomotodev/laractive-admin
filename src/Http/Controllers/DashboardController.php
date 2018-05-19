<?php

namespace Enomotodev\LaractiveAdmin\Http\Controllers;

use Illuminate\Support\HtmlString;

class DashboardController extends Controller
{
    /**
     * The default layout view.
     *
     * @var string
     */
    public static $defaultDashboardView = 'laractive-admin::dashboard';

    /**
     * @return string
     */
    public function index()
    {
        return new HtmlString(
            view()->make(static::$defaultDashboardView, [
                'class' => null,
                'table' => null,
                'layoutView' => static::$defaultLayoutView,
            ])->render()
        );
    }
}
