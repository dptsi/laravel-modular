<?php

namespace Dptsi\Modular\Providers;

use Dptsi\Modular\Base\Module;
use Dptsi\Modular\Console\ModuleMakeCommand;
use Dptsi\Modular\Console\ModuleMessagingTableCommand;
use Dptsi\Modular\Console\ModuleProvideBladeComponentCommand;
use Dptsi\Modular\Console\ModuleProvideDatabaseCommand;
use Dptsi\Modular\Console\ModuleProvideDependencyCommand;
use Dptsi\Modular\Console\ModuleProvideEventCommand;
use Dptsi\Modular\Console\ModuleProvideLangCommand;
use Dptsi\Modular\Console\ModuleProvideMessagingCommand;
use Dptsi\Modular\Console\ModuleProvideRouteCommand;
use Dptsi\Modular\Console\ModuleProvideViewCommand;
use Dptsi\Modular\Core\Manager;
use Dptsi\Modular\Event\EventManager;
use Dptsi\Modular\Exception\InvalidModuleClass;
use Dptsi\Modular\Facade\ModuleManager;
use Dptsi\Modular\Messaging\MessageBus;
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
        $this->app->singleton('module_manager', Manager::class);
        $this->app->singleton('event_manager', EventManager::class);
        $this->app->singleton('message_bus', MessageBus::class);
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
                    ModuleProvideRouteCommand::class,
                    ModuleProvideDatabaseCommand::class,
                    ModuleProvideViewCommand::class,
                    ModuleProvideLangCommand::class,
                    ModuleProvideBladeComponentCommand::class,
                    ModuleProvideDependencyCommand::class,
                    ModuleMessagingTableCommand::class,
                    ModuleProvideEventCommand::class,
                    ModuleProvideMessagingCommand::class,
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

            $module = new $module_properties['module_class'](
                $module_properties,
                $module_name == config('modules.default_module')
            );
            if (!($module instanceof Module)) {
                throw new InvalidModuleClass();
            }

            ModuleManager::register($module_name, $module);

            foreach ($module->getProviders() as $provider) {
                App::register($provider);
            }
        }
    }
}
