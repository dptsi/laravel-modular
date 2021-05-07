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

    Note: All namespace inside app directory (app/) will change into custom namespace

2. To create a new module, run:

    ```shell
    php artisan module:make ModuleName
    ```

    This will create a new module using mvc skeleton and sqlsrv database configuration.

    Note: the module name must be in StudlyCase
    
3. If you want to create a new modul using onion skeleton, run:

    ```shell
    php artisan module:make ModuleName --skeleton=onion
    ```
 
3. If you want to create a new modul using mysql database configuration, run:

    ```shell
    php artisan module:make ModuleName --database=mysql
    ```
