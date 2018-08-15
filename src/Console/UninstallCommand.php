<?php

namespace Enomotodev\LaractiveAdmin\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;

class UninstallCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'laractive-admin:uninstall';

    /**
     * @var string
     */
    protected $description = 'Uninstall LaractiveAdmin';

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
        $this->removeMigration();
        $this->removeAdminUser();

        $this->info('LaractiveAdmin uninstall successfully!');
    }

    /**
     * @return void
     */
    protected function removeMigration()
    {
        $migrations = [
            'create_admin_users_table',
            'create_laractive_admin_comments_table',
        ];

        $files = $this->files->files(database_path('migrations'));
        foreach ($files as $file) {
            if (in_array(substr($file->getFilename(), 18, -4), $migrations)) {
                unlink($file->getRealPath());
            }
        }
    }

    /**
     * @return void
     */
    protected function removeAdminUser()
    {
        $this->files->deleteDirectory(app_path('Admin'));
    }
}
