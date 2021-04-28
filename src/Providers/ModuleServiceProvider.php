<?php

namespace Dptsi\Modular\Providers;

use Dptsi\Modular\Base\Module;
use Dptsi\Modular\Console\ModuleMakeCommand;
use Dptsi\Modular\Exception\InvalidModuleClass;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publish();
        $this->registerCommands();
        $this->loadModules();
    }

    protected function publish()
    {
        $this->publishes(
            [
                __DIR__ . '/../config/modules.php' => config_path('modules.php'),
            ]
        );
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    ModuleMakeCommand::class,
                ]
            );
        }
    }

    protected function loadModules()
    {
        foreach (config('modules.modules') ?? [] as $module_name => $module_properties) {
            if (!$module_properties['enabled']) {
                continue;
            }

            $module = new $module_properties['moduleClass'];
            if (!($module instanceof Module)) {
                throw new InvalidModuleClass();
            }

            foreach ($module->getProviders() as $provider) {
                App::register($provider);
            }
        }
    }
}
