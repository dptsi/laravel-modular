<?php


namespace Dptsi\Modular\Console;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleProvideRouteCommand extends GeneratorCommand
{
    protected $name = 'module:provide-route';

    protected $description = 'Generate route service provider for a module';

    protected $type = 'Route service provider';

    protected function replaceClass($stub, $name)
    {
        $route_path = null;
        switch ($this->option('skeleton')) {
            case 'onion':
                $route_path = '../Presentation/routes';
                break;
            case 'mvc':
                $route_path = '../routes';
            default:
        }

        $stub = str_replace(['{{ route_path }}'], $route_path, $stub);

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

        return parent::handle();
    }

}