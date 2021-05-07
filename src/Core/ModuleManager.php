<?php


namespace Dptsi\Modular\Core;


use Dptsi\Modular\Base\BaseModule;

class ModuleManager
{
    /**
     * @var array<string, BaseModule>
     */
    private array $modules = [];

    public function register(string $module_config_name, BaseModule $module): void
    {
        $this->modules[$module_config_name] = $module;
    }

    /**
     * @param string $module_config_name
     * @return BaseModule
     */
    public function get(string $module_config_name): BaseModule
    {
        return $this->modules[$module_config_name];
    }

    /**
     * @return array<string, BaseModule>
     */
    public function all(): array
    {
        return $this->modules;
    }

    public function getDefault(): ?BaseModule
    {
        foreach ($this->modules as $module) {
            if ($module->isDefault()) {
                return $module;
            }
        }

        return null;
    }

    public function path(string $module_directory_name, string $path): string
    {
        return app_path('Modules/' . $module_directory_name . '/' . $path);
    }
}