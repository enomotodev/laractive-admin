<?php

namespace Enomotodev\LaractiveAdmin\Console;

use Illuminate\Console\Command;
use Enomotodev\LaractiveAdmin\AdminUser;

class SeedCommand extends Command
{
    /**
     * @var string
     */
    const ADMIN_USER_EMAIL = 'admin@example.com';

    /**
     * @var string
     */
    const ADMIN_USER_PASSWORD = 'password';

    /**
     * @var string
     */
    protected $name = 'laractive-admin:seed';

    /**
     * @var string
     */
    protected $description = 'Generate seed data';

    /**
     * @return void
     */
    public function handle()
    {
        if (! AdminUser::where('email', 'admin@example.com')->first()) {
            AdminUser::create([
                'email' => self::ADMIN_USER_EMAIL,
                'password' => \Hash::make(self::ADMIN_USER_PASSWORD),
            ]);
        }

        $this->info('Generate data successfully!');
    }
}
