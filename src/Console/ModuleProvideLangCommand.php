<?php


namespace Dptsi\Modular\Console;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Dptsi\Modular\Facade\Module;

class ModuleProvideLangCommand extends GeneratorCommand
{
    protected $name = 'module:provide-lang';

    protected $description = 'Generate lang service provider for a module';

    protected $type = 'Lang service provider';

    private $lang_path;

    public function setLangPath($lang_path)
    {
        $this->lang_path = $lang_path;
    }

    public function getLangPath()
    {
        return $this->lang_path;
    }

    protected function replaceClass($stub, $name)
    {
        switch ($this->option('skeleton')) {
            case 'onion':
                $this->setLangPath('../Presentation/resources/lang');
                break;
            case 'mvc':
            default:
                $this->setLangPath('../resources/lang');
        }

        $stub = str_replace(['{{ lang_path }}'], $this->getLangPath(), $stub);

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

        parent::handle();

        $stub_en = $this->files->get(__DIR__ . '/../stubs/skeleton/resources/lang/en/general.stub');
        $stub_id = $this->files->get(__DIR__ . '/../stubs/skeleton/resources/lang/id/general.stub');

        $stub_en = str_replace(
            ['{{ module_name }}'],
            $this->argument('name'), $stub_en
        );
        $stub_id = str_replace(
            ['{{ module_name }}'],
            $this->argument('name'), $stub_id
        );

        $path_en = Module::path($this->argument('name'), str_replace('../', '', $this->getLangPath()) . '/en/general.php');
        $path_id = Module::path($this->argument('name'), str_replace('../', '', $this->getLangPath()) . '/id/general.php');

        $this->files->put(
            $path_en, $stub_en
        );
        $this->files->put(
            $path_id, $stub_id
        );
    }
}