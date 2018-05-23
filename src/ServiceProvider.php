<?php

namespace Enomotodev\LaractiveAdmin;

use Collective\Html\HtmlServiceProvider;
use Collective\Html\FormFacade;
use Collective\Html\HtmlFacade;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Enomotodev\LaractiveAdmin\Http\Middleware\LaractiveAdminAuthenticate;
use Enomotodev\LaractiveAdmin\Console\InstallCommand;
use Enomotodev\LaractiveAdmin\Console\SeedCommand;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([$this->configPath() => config_path('laractive-admin.php')], 'config');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laractive-admin');

        $routeConfig = [
            'middleware' => ['web', 'laractive-admin'],
            'namespace' => 'App\Admin',
            'prefix' => $this->app['config']->get('laractive-admin.route_prefix'),
            'as' => 'admin.',
            'where' => [
                'id' => '[0-9]+',
            ],
        ];

        if (is_dir($directory = app_path('Admin'))) {
            $this->getRouter()->group($routeConfig, function ($router) {
                /** @var $router \Illuminate\Routing\Router */
                $files = $this->getFilesystem()->allFiles(app_path('Admin'));

                foreach ($files as $file) {
                    if ($file->getFilename() === 'DashboardController.php') {
                        continue;
                    }

                    $filename = $file->getFilename();
                    $className = substr($filename, 0, -4);
                    $adminClassName = "\App\Admin\\{$className}";
                    $adminClass = new $adminClassName;
                    $model = new $adminClass->model;
                    $routePrefix = $model->getTable();
                    $router->get("{$routePrefix}", [
                        'uses' => "\App\Admin\\{$className}@index",
                        'as' => "{$routePrefix}.index",
                    ]);
                    $router->get("{$routePrefix}/{id}", [
                        'uses' => "\App\Admin\\{$className}@show",
                        'as' => "{$routePrefix}.show",
                    ]);
                    $router->get("{$routePrefix}/new", [
                        'uses' => "\App\Admin\\{$className}@new",
                        'as' => "{$routePrefix}.new",
                    ]);
                    $router->post("{$routePrefix}", [
                        'uses' => "\App\Admin\\{$className}@create",
                        'as' => "{$routePrefix}.create",
                    ]);
                    $router->get("{$routePrefix}/{id}/edit", [
                        'uses' => "\App\Admin\\{$className}@edit",
                        'as' => "{$routePrefix}.edit",
                    ]);
                    $router->put("{$routePrefix}/{id}", [
                        'uses' => "\App\Admin\\{$className}@update",
                        'as' => "{$routePrefix}.update",
                    ]);
                    $router->delete("{$routePrefix}/{id}", [
                        'uses' => "\App\Admin\\{$className}@destroy",
                        'as' => "{$routePrefix}.destroy",
                    ]);

                    app(Menu::class)->setPage([
                        'name' => $className,
                        'url' => route("admin.{$routePrefix}.index"),
                    ]);
                }

                // Dashboard
                $router->get('/', [
                    'uses' => '\Enomotodev\LaractiveAdmin\Http\Controllers\DashboardController@index',
                    'as' => 'dashboard.index',
                ]);
            });
        }

        // Authentication
        $this->getRouter()->group([
            'middleware' => ['web'],
            'prefix' => $this->app['config']->get('laractive-admin.route_prefix'),
        ], function($router) {
            /** @var $router \Illuminate\Routing\Router */
            $router->get('login', [
                'uses' => '\Enomotodev\LaractiveAdmin\Http\Controllers\Auth\LoginController@showLoginForm',
                'as' => 'admin.login',
            ]);
            $router->post('login', [
                'uses' => '\Enomotodev\LaractiveAdmin\Http\Controllers\Auth\LoginController@login',
            ]);
            $router->get('logout', [
                'uses' => '\Enomotodev\LaractiveAdmin\Http\Controllers\Auth\LoginController@logout',
                'as' => 'admin.logout',
            ]);
        });
    }

    /**
     * Register a service provider with the application.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'laractive-admin');

        $this->app->register(HtmlServiceProvider::class);

        $this->app->alias('Form', FormFacade::class);
        $this->app->alias('Html', HtmlFacade::class);

        $this->app->singleton(Menu::class);

        $this->app['config']['auth.guards'] += [
            'laractive-admin' => [
                'driver' => 'session',
                'provider' => 'admin_users',
            ],
        ];
        $this->app['config']['auth.providers'] += [
            'admin_users' => [
                'driver' => 'eloquent',
                'model' => AdminUser::class,
            ],
        ];

        $this->getRouter()->aliasMiddleware('laractive-admin', LaractiveAdminAuthenticate::class);

        $this->app->singleton('command.laractive-admin.install', function ($app) {
            return new InstallCommand($app['files'], $app['composer']);
        });
        $this->app->singleton('command.laractive-admin.seed', function () {
            return new SeedCommand;
        });

        $this->commands(['command.laractive-admin.install']);
        $this->commands(['command.laractive-admin.seed']);
    }

    /**
     * @return string
     */
    protected function configPath()
    {
        return __DIR__ . '/../config/laractive-admin.php';
    }

    /**
     * Get the active router.
     *
     * @return \Illuminate\Routing\Router
     */
    protected function getRouter()
    {
        return $this->app['router'];
    }

    /**
     * Get the filesystem.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    protected function getFilesystem()
    {
        return $this->app['files'];
    }
}
