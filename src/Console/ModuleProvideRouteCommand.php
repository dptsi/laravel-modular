<?php


namespace Dptsi\Modular\Console;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Dptsi\Modular\Facade\ModuleManager;

class ModuleProvideRouteCommand extends GeneratorCommand
{
    protected $name = 'module:provide-route';

    protected $description = 'Generate route service provider for a module';

    protected $type = 'Route service provider';

    private $controller_path;

    private $route_path;

    public function setControllerPath($controller_path)
    {
        $this->controller_path = $controller_path;
    }

    public function getControllerPath()
    {
        return $this->controller_path;
    }

    public function setRoutePath($route_path)
    {
        $this->route_path = $route_path;
    }

    public function getRoutePath()
    {
        return $this->route_path;
    }

    protected function replaceClass($stub, $name)
    {
        switch ($this->option('skeleton')) {
            case 'onion':
                $this->setControllerPath('../Presentation/Controllers');
                $this->setRoutePath('../Presentation/routes');
                break;
            case 'mvc':
            default:
                $this->setControllerPath('../Controllers');
                $this->setRoutePath('../routes');
        }

        $stub = str_replace(['{{ route_path }}'], $this->getRoutePath(), $stub);

        return str_replace(['{{ module_name }}'], Str::kebab($this->argument('name')), $stub);
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

        $controller_stub = $this->files->get(__DIR__ . '/../stubs/skeleton/controllers/BaseController.stub');

        $controller_stub = str_replace(
            ['{{ module_name }}'],
            $this->argument('name'), $controller_stub
        );

        $controller_stub = str_replace(
            ['DummyNamespace'],
            $this->laravel->getNamespace() . 'Modules\\' . $this->argument('name') . str_replace('/', '\\', str_replace('..', '', $this->getControllerPath())),
            $controller_stub
        );

        $controller_path = ModuleManager::path($this->argument('name'), str_replace('../', '', $this->getControllerPath()) . '/BaseController.php');

        $this->files->put(
            $controller_path, $controller_stub
        );

        $route_stub = $this->files->get(__DIR__ . '/../stubs/skeleton/routes/web.stub');

        $route_stub = str_replace(
            ['DummyNamespace'],
            $this->laravel->getNamespace() . 'Modules\\' . $this->argument('name') . str_replace('/', '\\', str_replace('..', '', $this->getControllerPath())) . '\BaseController',
            $route_stub
        );

        $route_path = ModuleManager::path($this->argument('name'), str_replace('../', '', $this->getRoutePath()) . '/web.php');

        $this->files->put(
            $route_path, $route_stub
        );
    }
}