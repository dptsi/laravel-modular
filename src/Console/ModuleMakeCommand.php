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
                'S',
                InputOption::VALUE_OPTIONAL,
                'Folder structure to be applied to the module',
                'mvc',
            ],
            [
                'database',
                'D',
                InputOption::VALUE_OPTIONAL,
                'Database driver to be applied to the module',
                'sqlsrv',
            ],
        ];
    }

    public function handle()
    {
        if (!in_array($this->option('skeleton'), ['onion', 'mvc'])) {
            $this->error('Skeleton type is not registered');
            return false;
        }
        if (!in_array($this->option('database'), ['sqlsrv', 'mysql'])) {
            $this->error('Database driver is not registered');
            return false;
        }
        $this->prepareProviders();
        $this->copySkeleton();
        $this->createModuleConfig();
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
                '--database' => $this->option('database'),
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
        $this->call(
            'module:provide-event',
            [
                'name' => $this->argument('name'),
                '--skeleton' => $this->option('skeleton'),
            ]
        );
        $this->call(
            'module:provide-messaging',
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

    private function createModuleConfig(): void
    {
        if (!$this->files->isFile(config_path('modules.php')))
            $this->files->copy(__DIR__ . '/../config/modules.php', config_path('modules.php'));

        $module_config = require config_path('modules.php');

        $module_config['modules'][Str::snake($this->argument('name'))] = [
            'module_class' => 'App\\Modules\\'.Str::studly($this->argument('name')).'\\Module',
            'enabled' => true,
        ];

        ob_start();
        echo "<?php\n\n";
        echo "return [\n";
        if ($module_config['default_module']) {
            echo "\n\t'default_module' => '{$module_config['default_module']}',";
        } else {
            echo "\n\t'default_module' => null,";
        }
        echo "\n\t'modules' => [";
        foreach ($module_config['modules'] as $key => $value) {
            echo "\n\t\t'{$key}' => [";
                echo "\n\t\t\t'module_class' => '{$value['module_class']}',";
                echo "\n\t\t\t'enabled' => {$value['enabled']},";
            echo "\n\t\t],";
        }
        echo "\n\t],\n];";
        $output = ob_get_clean();

        $this->files->put(
            config_path('modules.php'), $output
        );
    }
}