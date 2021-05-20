<?php


namespace Dptsi\Modular\Console;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleProvideDatabaseCommand extends GeneratorCommand
{
    protected $hidden = true;

    protected $name = 'module:provide-database';

    protected $description = 'Generate database service provider for a module';

    protected $type = 'Database service provider';

    protected function replaceClass($stub, $name)
    {
        return str_replace(['{{ module_name }}'], Str::snake($this->argument('name')), $stub);
    }

    protected function getStub()
    {
        return __DIR__ . '/../stubs/DatabaseServiceProvider.stub';
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '/Providers/DatabaseServiceProvider.php';
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
                'database',
                null,
                InputOption::VALUE_OPTIONAL,
                'Database driver to be applied to the module',
                'sqlsrv',
            ],
        ];
    }

    public function handle()
    {
        if (!in_array($this->option('database'), ['sqlsrv', 'mysql', 'pgsql'])) {
            $this->error('Database driver is not registered');
            return false;
        }

        $module_name = Str::snake($this->argument('name'));

        $this->files->ensureDirectoryExists(config_path($module_name));

        switch ($this->option('database')) {
            case 'mysql':
                $stub = $this->files->get(__DIR__ . '/../stubs/database/module.database.mysql.stub');
                break;
            case 'pgsql':
                $stub = $this->files->get(__DIR__ . '/../stubs/database/module.database.pgsql.stub');
                break;
            case 'sqlsrv':
            default:
                $stub = $this->files->get(__DIR__ . '/../stubs/database/module.database.sqlsrv.stub');
        }

        $stub = str_replace(
            ['{{ module_name }}'],
            Str::upper($module_name), $stub
        );

        $this->files->put(
            config_path($module_name . '/database.php'), $stub
        );

        return parent::handle();
    }
}