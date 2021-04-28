<?php


namespace Dptsi\Modular\Console;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class ModuleProvideDatabaseCommand extends GeneratorCommand
{
    protected $name = 'module:provide-database';

    protected $description = 'Generate database service provider for a module';

    protected $type = 'Database service provider';

    protected function replaceClass($stub, $name)
    {
        return str_replace(['{{ module_name }}'], Str::snake($this->argument('name')), $stub);
    }

    protected function getStub()
    {
        return __DIR__ . '/../stubs/DatabaseServiceProvider.stub';
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '/Providers/DatabaseServiceProvider.php';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return parent::getDefaultNamespace($rootNamespace) . '\\Modules';
    }

    protected function getNamespace($name)
    {
        return $name . "\\Providers";
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the module'],
        ];
    }

    public function handle()
    {
        $module_snake_name = Str::snake($this->argument('name'));
        $this->files->ensureDirectoryExists(config_path($module_snake_name));

        $this->files->copy(
            __DIR__ . '/../config/module.database.php',
            config_path($module_snake_name . '/database.php')
        );

        return parent::handle();
    }
}