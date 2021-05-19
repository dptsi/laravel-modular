<?php

namespace Dptsi\Modular\Providers;

use Dptsi\Modular\Base\BaseModule;
use Dptsi\Modular\Console\ChangeAppNameCommand;
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
use Dptsi\Modular\Core\ModuleManager;
use Dptsi\Modular\Event\EventManager;
use Dptsi\Modular\Messaging\MessageBus;
use Dptsi\Modular\Exception\InvalidModuleClass;
use Dptsi\Modular\Facade\Module;
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
        $this->app->singleton('module_manager', ModuleManager::class);
        $this->app->singleton('event_manager', EventManager::class);
        $this->app->singleton('message_bus', MessageBus::class);
        $this->loadModules();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerCommands();
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    ChangeAppNameCommand::class,
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
            if (!($module instanceof BaseModule)) {
                throw new InvalidModuleClass();
            }

            Module::register($module_name, $module);

            foreach ($module->getProviders() as $provider) {
                $this->app->register($provider);
            }
        }
    }
}
