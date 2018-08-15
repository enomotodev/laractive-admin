<?php

namespace Enomotodev\LaractiveAdmin\Tests;

use Collective\Html\FormFacade;
use Collective\Html\HtmlFacade;
use Illuminate\Support\Facades\Hash;
use Collective\Html\HtmlServiceProvider;
use Illuminate\Contracts\Console\Kernel;
use Enomotodev\LaractiveAdmin\ServiceProvider;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * @var string
     */
    protected $baseUrl = 'http://localhost:8000';

    /**
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Form', FormFacade::class);
            $loader->alias('Html', HtmlFacade::class);
        });

        $app->make(Kernel::class)->bootstrap();

        $app->register(HtmlServiceProvider::class);
        $app->register(ServiceProvider::class);

        return $app;
    }

    public function setUp()
    {
        $this->copyAdminFile();
        $this->copyDashboardFile();

        parent::setUp();

        $this->app['config']->set('app.key', 'base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=');
        $this->app['config']->set('database.default', env('DB_CONNECTION'));
        $this->app['config']->set('database.connections.mysql.host', env('DB_HOST'));
        $this->app['config']->set('database.connections.mysql.port', env('DB_PORT'));
        $this->app['config']->set('database.connections.mysql.database', env('DB_DATABASE'));
        $this->app['config']->set('database.connections.mysql.username', env('DB_USERNAME'));
        $this->app['config']->set('database.connections.mysql.password', env('DB_PASSWORD'));

        $this->copyMigrationFile();

        $this->artisan('migrate:refresh');

        \Enomotodev\LaractiveAdmin\AdminUser::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    public function tearDown()
    {
        $this->artisan('migrate:reset');

        parent::tearDown();
    }

    protected function copyMigrationFile()
    {
        $migrations = [
            'create_admin_users_table',
            'create_laractive_admin_comments_table',
        ];

        foreach ($migrations as $migration) {
            $this->app['files']->put(
                database_path("migrations/2018_01_01_000000_{$migration}.php"),
                $this->app['files']->get(__DIR__."/../src/Console/stubs/{$migration}.stub")
            );
        }
    }

    protected function copyAdminFile()
    {
        if (! is_dir($directory = __DIR__.'/../vendor/laravel/laravel/app/Admin')) {
            mkdir($directory, 0755, true);
        }

        copy(__DIR__.'/../src/Console/stubs/AdminUser.stub', "{$directory}/AdminUser.php");
    }

    protected function copyDashboardFile()
    {
        if (! is_dir($directory = __DIR__.'/../vendor/laravel/laravel/app/Admin')) {
            mkdir($directory, 0755, true);
        }

        copy(__DIR__.'/../src/Console/stubs/Dashboard.stub', "{$directory}/Dashboard.php");

        if (! is_dir($directory = __DIR__.'/../vendor/laravel/laravel/resources/views/admin')) {
            mkdir($directory, 0755, true);
        }

        copy(__DIR__.'/../src/Console/stubs/dashboard.blade.stub', "{$directory}/dashboard.blade.php");
    }
}
