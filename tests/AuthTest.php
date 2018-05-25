<?php

namespace Enomotodev\LaractiveAdmin\Tests;

class AuthTest extends TestCase
{
    public function testLoginPage()
    {
        $this->visit('admin/login')
            ->see('login');
    }

    public function testVisitWithoutLogin()
    {
        $this->visit('admin')
            ->dontSeeIsAuthenticated('laractive-admin')
            ->seePageIs('admin/login');
    }

    public function testLoginAndLogout()
    {
        $credentials = ['email' => 'admin@example.com', 'password' => 'password'];

        $this->visit('admin/login')
            ->see('login')
            ->submitForm('Login', $credentials)
            ->see('dashboard')
            ->seeCredentials($credentials, 'laractive-admin')
            ->seeIsAuthenticated('laractive-admin')
            ->seePageIs('admin')
            ->visit('admin/logout')
            ->seePageIs('admin/login')
            ->dontSeeIsAuthenticated('laractive-admin');
    }

    public function testLogoutWithoutLogin()
    {
        $this->visit('admin/logout')
            ->seePageIs('admin/login')
            ->dontSeeIsAuthenticated('laractive-admin');
    }
}
