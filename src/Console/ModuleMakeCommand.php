<?php


namespace Dptsi\Modular\Console;


use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class ModuleMakeCommand extends GeneratorCommand
{
    protected $name = 'module:make';

    protected $description = 'Create new module';

    protected $type = 'Module';

    protected function replaceClass($stub, $name)
    {
        return $stub;
    }

    protected function getStub()
    {
        return __DIR__ . '/../stubs/module.stub';
    }

    protected function getPath($name)
    {
        return $this->laravel['path'] . '/Modules/' . $name . '/Module.php';
    }

    protected function getNamespace($name)
    {
        return parent::getNamespace($name) . '\\' . $name;
    }

    protected function rootNamespace()
    {
        return parent::rootNamespace() . 'Modules';
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the module'],
        ];
    }
}