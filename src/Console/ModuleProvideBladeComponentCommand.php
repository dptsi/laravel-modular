<?php


namespace Dptsi\Modular\Console;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Dptsi\Modular\Facade\Module;

class ModuleProvideBladeComponentCommand extends GeneratorCommand
{
    protected $name = 'module:provide-blade';

    protected $description = 'Generate blade service provider for a module';

    protected $type = 'Blade service provider';

    private $component_path;

    private $component_namespace;

    public function setComponentPath($component_path)
    {
        $this->component_path = $component_path;
    }

    public function getComponentPath()
    {
        return $this->component_path;
    }

    public function setComponentNamespace($component_namespace)
    {
        $this->component_namespace = $component_namespace;
    }

    public function getComponentNamespace()
    {
        return $this->component_namespace;
    }

    protected function replaceClass($stub, $name)
    {
        switch ($this->option('skeleton')) {
            case 'onion':
                $this->setComponentNamespace(str_replace('\\', '\\\\', $this->laravel->getNamespace()) . 'Modules\\\\' . Str::studly($this->argument('name')) . '\\\\Presentation\\\\Components');
                $this->setComponentPath('../Presentation/Components');
                break;
            case 'mvc':
            default:
                $this->setComponentNamespace(str_replace('\\', '\\\\', $this->laravel->getNamespace()) . 'Modules\\\\' . Str::studly($this->argument('name')) . '\\\\Components');
                $this->setComponentPath('../Components');
        }
        $stub = str_replace(
            ['{{ SkeletonNamespace }}'],
            $this->getComponentNamespace(),
            $stub
        );
        return str_replace(['{{ module_name }}'], Str::studly($this->argument('name')), $stub);
    }

    protected function getStub()
    {
        return __DIR__ . '/../stubs/BladeComponentServiceProvider.stub';
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '/Providers/BladeComponentServiceProvider.php';
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

        $stub = $this->files->get(__DIR__ . '/../stubs/skeleton/components/Alert.stub');

        $stub = str_replace(
            ['{{ module_name }}'],
            $this->argument('name'), $stub
        );

        $stub = str_replace(
            ['DummyNamespace'],
            str_replace('\\\\', '\\', $this->getComponentNamespace()), $stub
        );

        $path = Module::path($this->argument('name'), str_replace('../', '', $this->getComponentPath()) . '/Alert.php');

        $this->files->put(
            $path, $stub
        );
    }

}