<?php


namespace Dptsi\Modular\Console;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Dptsi\Modular\Facade\Module;

class ModuleProvideViewCommand extends GeneratorCommand
{
    protected $name = 'module:provide-view';

    protected $description = 'Generate view service provider for a module';

    protected $type = 'View service provider';

    private $view_path;

    public function setViewPath($view_path)
    {
        $this->view_path = $view_path;
    }

    public function getViewPath()
    {
        return $this->view_path;
    }

    protected function replaceClass($stub, $name)
    {
        switch ($this->option('skeleton')) {
            case 'onion':
                $this->setViewPath('../Presentation/resources/views');
                break;
            case 'mvc':
                $this->setViewPath('../resources/views');
            default:
        }

        $stub = str_replace(['{{ view_path }}'], $this->getViewPath(), $stub);

        return str_replace(['{{ module_name }}'], Str::studly($this->argument('name')), $stub);
    }

    protected function getStub()
    {
        return __DIR__ . '/../stubs/ViewServiceProvider.stub';
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '/Providers/ViewServiceProvider.php';
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

    protected function getOptions()
    {
        return [
            [
                'skeleton',
                null,
                InputOption::VALUE_OPTIONAL,
                'Folder structure to be applied to the module',
                'mvc',
            ],
        ];
    }

    public function handle()
    {
        if (!in_array($this->option('skeleton'), ['onion', 'mvc'])) {
            $this->error('Skeleton type is not registered');
            return false;
        }

        parent::handle();

        $stub = $this->files->get(__DIR__ . '/../stubs/skeleton/resources/views/welcome.blade.stub');

        $stub = str_replace(
            ['{{ module_name }}'],
            $this->argument('name'), $stub
        );

        $path = Module::path($this->argument('name'), str_replace('../', '', $this->getViewPath()) . '/welcome.blade.php');

        $this->files->put(
            $path, $stub
        );

        $stub = $this->files->get(__DIR__ . '/../stubs/skeleton/resources/views/components/alert.blade.stub');

        $stub = str_replace(
            ['{{ module_name }}'],
            $this->argument('name'), $stub
        );

        $path = Module::path($this->argument('name'), str_replace('../', '', $this->getViewPath()) . '/components/alert.blade.php');

        $this->files->put(
            $path, $stub
        );
    }
}