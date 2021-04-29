<?php


namespace Dptsi\Modular\Console;


use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class ModuleMessagingTableCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:messaging-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a migration for the messaging tracker database table';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected Filesystem $files;

    /**
     * @var Composer
     */
    protected Composer $composer;

    /**
     * Create a new queue job table command instance.
     *
     * @param Filesystem $files
     * @param Composer $composer
     * @return void
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $table = 'message_identity_tracking';

        $this->replaceMigration(
            $this->createBaseMigration($table),
            $table,
            Str::studly($table)
        );

        $this->info('Migration created successfully!');

        $this->composer->dumpAutoloads();
    }

    /**
     * Create a base migration file for the table.
     *
     * @param string $table
     * @return string
     */
    protected function createBaseMigration(string $table): string
    {
        return $this->laravel['migration.creator']->create(
            'create_' . $table . '_table',
            $this->laravel->databasePath() . '/migrations'
        );
    }

    /**
     * Replace the generated migration with the job table stub.
     *
     * @param string $path
     * @param string $table
     * @param string $tableClassName
     * @return void
     * @throws FileNotFoundException
     */
    protected function replaceMigration(string $path, string $table, string $tableClassName)
    {
        $stub = str_replace(
            ['{{table}}', '{{tableClassName}}'],
            [$table, $tableClassName],
            $this->files->get(__DIR__ . '/../stubs/message_identity_tracking.stub')
        );

        $this->files->put($path, $stub);
    }
}
