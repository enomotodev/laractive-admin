<?php

namespace Enomotodev\LaractiveAdmin\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class InstallCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'laractive-admin:install';

    /**
     * @var string
     */
    protected $description = 'Install LaractiveAdmin';

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  \Illuminate\Support\Composer  $composer
     * @return void
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $this->createMigration();
        $this->createAdminUser();
        $this->createDashboard();

        $this->info('LaractiveAdmin install successfully!');
    }

    /**
     * @return void
     */
    protected function createMigration()
    {
        $migrations = [
            'create_admin_users_table',
            'create_laractive_admin_comments_table',
        ];

        try {
            foreach ($migrations as $migration) {
                $fullPath = $this->createBaseMigration($migration);
                $this->files->put($fullPath, $this->files->get(__DIR__."/stubs/{$migration}.stub"));
            }
            $this->composer->dumpAutoloads();
        } catch (FileNotFoundException $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * @param  string  $name
     * @return mixed
     */
    protected function createBaseMigration($name)
    {
        $path = $this->laravel->databasePath().'/migrations';

        return $this->laravel['migration.creator']->create($name, $path);
    }

    /**
     * @return void
     */
    protected function createAdminUser()
    {
        if (! is_dir($directory = app_path('Admin'))) {
            mkdir($directory, 0755, true);
        }

        copy(__DIR__.'/stubs/AdminUser.stub', "{$directory}/AdminUser.php");
    }

    /**
     * @return void
     */
    protected function createDashboard()
    {
        if (! is_dir($directory = app_path('Admin'))) {
            mkdir($directory, 0755, true);
        }

        copy(__DIR__.'/stubs/Dashboard.stub', "{$directory}/Dashboard.php");

        if (! is_dir($directory = resource_path('views/admin'))) {
            mkdir($directory, 0755, true);
        }

        copy(__DIR__.'/stubs/dashboard.blade.stub', "{$directory}/dashboard.blade.php");
    }
}
