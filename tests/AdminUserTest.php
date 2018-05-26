<?php

namespace Enomotodev\LaractiveAdmin\Tests;

class AdminUserTest extends TestCase
{
    private $credentials;
    private $adminUser;

    public function setUp()
    {
        parent::setUp();

        $this->credentials = ['email' => 'admin@example.com', 'password' => 'password'];
        $this->adminUser = \Enomotodev\LaractiveAdmin\AdminUser::where(['email' => 'admin@example.com'])->first();
    }

    public function testIndex()
    {
        $this->visit('admin/login')
            ->submitForm('Login', $this->credentials)
            ->visit('admin/admin_users')
            ->see($this->adminUser->id)
            ->see($this->adminUser->email)
            ->see($this->adminUser->password)
            ->see($this->adminUser->created_at)
            ->see($this->adminUser->updated_at);
    }

    public function testShow()
    {
        $this->visit('admin/login')
            ->submitForm('Login', $this->credentials)
            ->visit("admin/admin_users/{$this->adminUser->id}")
            ->see($this->adminUser->id)
            ->see($this->adminUser->email)
            ->see($this->adminUser->password)
            ->see($this->adminUser->created_at)
            ->see($this->adminUser->updated_at);
    }

    public function testCreate()
    {
        $otherEmail = 'admin+1@example.com';

        $this->visit('admin/login')
            ->submitForm('Login', $this->credentials)
            ->visit('admin/admin_users/new')
            ->submitForm('Submit', [
                'email' => '',
                'password' => '',
            ])
            ->seePageIs('admin/admin_users/new')
            ->see('The email field is required.')
            ->see('The password field is required.')
            ->submitForm('Submit', [
                'email' => 'admin',
                'password' => 'password',
            ])
            ->seePageIs('admin/admin_users/new')
            ->see('The email must be a valid email address.')
            ->submitForm('Submit', [
                'email' => 'admin@example.com',
                'password' => 'password',
            ])
            ->seePageIs('admin/admin_users/new')
            ->see('The email has already been taken.')
            ->submitForm('Submit', [
                'email' => $otherEmail,
                'password' => 'password',
            ])
            ->seePageIs(
                'admin/admin_users/'.\Enomotodev\LaractiveAdmin\AdminUser::where(['email' => $otherEmail])->first()->id
            );
    }
}
