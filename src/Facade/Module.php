<?php


namespace Dptsi\Modular\Facade;


use Illuminate\Support\Facades\Facade;

/**
 * Class Module
 * @package Dptsi\Modular\Facade
 * @method static void register(string $module_config_name, \Dptsi\Modular\Base\BaseModule $module)
 * @method static \Dptsi\Modular\Base\BaseModule get(string $module_config_name)
 * @method static \Dptsi\Modular\Base\BaseModule[] all(string $module_name)
 * @method static string path(string $module_directory_name, string $path)
 * @method static \Dptsi\Modular\Base\BaseModule|null getDefault()
 */
class Module extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'module';
    }
}
