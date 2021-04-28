<?php


namespace Dptsi\Modular\Console;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
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
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '/Module.php';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return parent::getDefaultNamespace($rootNamespace) . '\\Modules';
    }

    protected function getNamespace($name)
    {
        return $name;
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the module'],
        ];
    }

    public function handle()
    {
        $this->call(
            'module:provide-route',
            [
                'name' => $this->argument('name'),
            ]
        );
        $this->call(
            'module:provide-database',
            [
                'name' => $this->argument('name'),
            ]
        );
        $this->call(
            'module:provide-view',
            [
                'name' => $this->argument('name'),
            ]
        );
        return parent::handle();
    }
}