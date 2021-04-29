<?php


namespace Dptsi\Modular\Console;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleProvideLangCommand extends GeneratorCommand
{
    protected $name = 'module:provide-lang';

    protected $description = 'Generate lang service provider for a module';

    protected $type = 'Lang service provider';

    protected function replaceClass($stub, $name)
    {
        $lang_path = null;
        switch ($this->option('skeleton')) {
            case 'onion':
                $lang_path = '../Presentation/lang';
                break;
            case 'mvc':
                $lang_path = '../resources/lang';
            default:
        }

        $stub = str_replace(['{{ lang_path }}'], $lang_path, $stub);

        return str_replace(['{{ module_name }}'], Str::studly($this->argument('name')), $stub);
    }

    protected function getStub()
    {
        return __DIR__ . '/../stubs/LangServiceProvider.stub';
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '/Providers/LangServiceProvider.php';
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