<?php


namespace Dptsi\Modular\Facade;


use Illuminate\Support\Facades\Facade;
use Dptsi\Modular\Base\Module;

/**
 * Class ModuleManager
 * @package Dptsi\Modular\Facade
 *
 * @method static void register(string $module_name, Module $module)
 * @method static Module get(string $module_name)
 * @method static Module[] all(string $module_name)
 * @method static string path(string $module_directory_name, string $path)
 */
class ModuleManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'module_manager';
    }
}