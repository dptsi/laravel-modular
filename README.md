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

1. (Optional)To change application namespace, run:

    ```shell
    php artisan app:name Custom\Namespace
    ```

    Note: All namespace inside app directory (app/) will change into custom namespace

2. To create a new module, run:

    ```shell
    php artisan module:make ModuleName
    ```

    Note: Module name must be in studly case