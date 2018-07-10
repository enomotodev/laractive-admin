<?php

namespace Enomotodev\LaractiveAdmin\Tests\Console;

use Enomotodev\LaractiveAdmin\AdminUser;
use Enomotodev\LaractiveAdmin\Console\SeedCommand;
use Enomotodev\LaractiveAdmin\Tests\TestCase;

class SeedCommandTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate:refresh');
    }

    public function testHandle()
    {
        $this->artisan('laractive-admin:seed');

        $user = AdminUser::first();

        $this->assertNotNull($user);
        $this->assertEquals(SeedCommand::ADMIN_USER_EMAIL, $user->email);
    }
}
