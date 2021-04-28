<?php


namespace Dptsi\Modular\Console;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class ModuleProvideRouteCommand extends GeneratorCommand
{
    protected $name = 'module:provide-route';

    protected $description = 'Generate route service provider for a module';

    protected $type = 'Route service provider';

    protected function replaceClass($stub, $name)
    {
        return str_replace(['{{ module_name }}'], Str::snake($this->argument('name')), $stub);
    }

    protected function getStub()
    {
        return __DIR__ . '/../stubs/RouteServiceProvider.stub';
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '/Providers/RouteServiceProvider.php';
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
}