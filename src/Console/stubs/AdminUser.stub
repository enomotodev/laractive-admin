<?php

namespace App\Admin;

use Enomotodev\LaractiveAdmin\Http\Controllers\Controller;

class AdminUser extends Controller
{
    /**
     * @var string
     */
    public $model = \Enomotodev\LaractiveAdmin\AdminUser::class;

    /**
     * @var array
     */
    protected $validate = [
        'email' => 'required|email|unique:admin_users',
        'password' => 'required',
    ];
}
