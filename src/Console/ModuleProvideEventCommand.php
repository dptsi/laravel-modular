<?php


namespace Dptsi\Modular\Console;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleProvideEventCommand extends GeneratorCommand
{
    protected $name = 'module:provide-event';

    protected $description = 'Generate event service provider for a module';

    protected $type = 'Event service provider';

    protected function replaceClass($stub, $name)
    {
        $event_listeners_path = null;
        switch ($this->option('skeleton')) {
            case 'onion':
                $event_listeners_path = 'Core/Application/EventListeners';
                break;
            case 'mvc':
                $event_listeners_path = 'Listeners';
            default:
        }

        $stub = str_replace(['{{ event_listeners_path }}'], $event_listeners_path, $stub);

        return str_replace(['{{ module_name }}'], Str::studly($this->argument('name')), $stub);
    }

    protected function getStub()
    {
        return __DIR__ . '/../stubs/EventServiceProvider.stub';
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '/Providers/EventServiceProvider.php';
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