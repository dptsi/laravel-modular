<?php


namespace Dptsi\Modular\Console;


use Dptsi\Modular\Facade\ModuleManager;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

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
        $this->prepareProviders();
        $this->copySkeleton();
        return parent::handle();
    }

    private function prepareProviders(): void
    {
        $this->call(
            'module:provide-route',
            [
                'name' => $this->argument('name'),
                '--skeleton' => $this->option('skeleton'),
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
                '--skeleton' => $this->option('skeleton'),
            ]
        );
        $this->call(
            'module:provide-lang',
            [
                'name' => $this->argument('name'),
                '--skeleton' => $this->option('skeleton'),
            ]
        );
        $this->call(
            'module:provide-blade',
            [
                'name' => $this->argument('name'),
                '--skeleton' => $this->option('skeleton'),
            ]
        );
        $this->call(
            'module:provide-dependency',
            [
                'name' => $this->argument('name'),
            ]
        );
    }

    private function copySkeleton(): void
    {
        $skeleton_dir = __DIR__ . '/../Skeleton/' . Str::studly($this->option('skeleton'));
        $module_path = ModuleManager::path($this->argument('name'), '');
        foreach (scandir($skeleton_dir) as $dir) {
            if (in_array($dir, ['.', '..'])) continue;
            $source_dir = $skeleton_dir . '/' . $dir;
            $this->files->copyDirectory($source_dir, $module_path . '/' . $dir);
        }
    }
}