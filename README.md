# Laractive Admin

[![Latest Stable Version](https://poser.pugx.org/enomotodev/laractive-admin/v/stable.png)](https://packagist.org/packages/enomotodev/laractive-admin)
[![Build Status](https://api.travis-ci.org/enomotodev/laractive-admin.svg?branch=master)](https://travis-ci.org/enomotodev/laractive-admin)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/enomotodev/laractive-admin/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/enomotodev/laractive-admin)
[![Code Coverage](https://scrutinizer-ci.com/g/enomotodev/laractive-admin/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/enomotodev/laractive-admin)

Laractive Admin is a Laravel framework for
creating elegant backends for website administration.

## Requirements

- **Laravel**: >= 5.5

## Install

Require this package with composer using the following command:

```bash
composer require enomotodev/laractive-admin
```

Run the installer

```bash
php artisan laractive-admin:install
```

The installer creates an initializer used for configuring defaults used by Laractive Admin as well as a new folder at app/Admin to put all your admin configurations.

Migrate your database

```bash
php artisan migrate
```

Seed admin user

```bash
php artisan laractive-admin:seed
```

Visit http://yourdomain.com/admin and log in using:

- **User**: admin@example.com
- **Password**: password

If you want to customize route prefix, Copy the package config to your local config with the publish command:

```bash
php artisan vendor:publish --provider="Enomotodev\LaractiveAdmin\ServiceProvider"
```

And edit `config/laractive-admin.php` file

```php
<?php

return [
    'route_prefix' => 'example-admin',
];
```

Then you can access with http://yourdomain.com/example-admin

## License

Laractive Admin is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
