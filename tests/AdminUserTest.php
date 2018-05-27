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

    public function testUpdate()
    {
        $otherEmail = 'admin+1@example.com';

        $this->visit('admin/login')
            ->submitForm('Login', $this->credentials)
            ->visit('admin/admin_users/new')
            ->submitForm('Submit', [
                'email' => $otherEmail,
                'password' => 'password',
            ]);

        $otherAdminUser = \Enomotodev\LaractiveAdmin\AdminUser::where(['email' => $otherEmail])->first();

        $this->visit("admin/admin_users/{$otherAdminUser->id}/edit")
            ->submitForm('Submit', [
                'email' => '',
                'password' => '',
            ])
            ->seePageIs("admin/admin_users/{$otherAdminUser->id}/edit")
            ->see('The email field is required.')
            ->see('The password field is required.')
            ->submitForm('Submit', [
                'email' => 'admin',
                'password' => 'password',
            ])
            ->seePageIs("admin/admin_users/{$otherAdminUser->id}/edit")
            ->see('The email must be a valid email address.')
            ->submitForm('Submit', [
                'email' => 'admin@example.com',
                'password' => 'password',
            ])
            ->seePageIs("admin/admin_users/{$otherAdminUser->id}/edit")
            ->see('The email has already been taken.')
            ->submitForm('Submit', [
                'email' => 'admin+2@example.com',
                'password' => 'password',
            ])
            ->seePageIs("admin/admin_users/{$otherAdminUser->id}")
            ->see('admin+2@example.com');
    }

    public function testDestroy()
    {
        $this->assertEquals(1, \Enomotodev\LaractiveAdmin\AdminUser::count());

        $this->visit('admin/login')
            ->submitForm('Login', $this->credentials)
            ->visit('admin/admin_users')
            ->submitForm('Delete');

        $this->assertEquals(0, \Enomotodev\LaractiveAdmin\AdminUser::count());
    }

    public function testComment()
    {
        $comment = 'This is comment';

        $this->visit('admin/login')
            ->submitForm('Login', $this->credentials)
            ->visit("admin/admin_users/{$this->adminUser->id}")
            ->submitForm('Submit', [
                'body' => $comment,
            ])
            ->visit("admin/admin_users/{$this->adminUser->id}")
            ->see($comment);
    }
}
