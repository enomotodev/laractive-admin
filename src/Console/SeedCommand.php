<?php

namespace Enomotodev\LaractiveAdmin\Console;

use Illuminate\Console\Command;
use Enomotodev\LaractiveAdmin\AdminUser;

class SeedCommand extends Command
{
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
        if (!AdminUser::where('email', 'admin@example.com')->first()) {
            AdminUser::create([
                'email' => 'admin@example.com',
                'password' => \Hash::make('password'),
            ]);
        }

        $this->info('Generate data successfully!');
    }
}