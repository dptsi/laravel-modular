# Laravel Modular

A package to generate modules for a Laravel project.

## Requirements

1. PHP 7.4 or greater
2. Laravel version 8

## Installation

Install using composer:

```shell
composer require dptsi/laravel-modular
```

## Usage

1. (Optional) To change application namespace, run:

    ```shell
    php artisan app:name Custom\Namespace
    ```

    Note: All namespace inside app directory (app/) will change into custom namespace.

    Note: in Mac OS or Linux use double backslash. `Custom\\Namespace`.

2. To create a new module, run:

    ```shell
    php artisan module:make ModuleName
    ```

    By default this will create a new module using mvc skeleton and sqlsrv database configuration.

    Note: the module name must be in StudlyCase

3. If you want to create a new module with a specific skeleton you can use `--skeleton` or `-S` option. Supported skeleton `onion` and `mvc`.

    ```shell
    php artisan module:make ModuleName --skeleton onion
    ```

    ```shell
    php artisan module:make ModuleName -S onion
    ```

4. If you want to create a new modul using specific database you can use `--database` or `-D` option.  Supported database `sqlsrv`, `mysql` and `pgsql`.

    ```shell
    php artisan module:make ModuleName --database mysql
    ```

    ```shell
    php artisan module:make ModuleName --D pgsql
    ```
