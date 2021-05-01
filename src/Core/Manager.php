<?php


namespace Dptsi\Modular\Core;


use Dptsi\Modular\Base\Module;

class Manager
{
    /**
     * @var array<string, Module>
     */
    private array $modules = [];

    public function register(string $module_config_name, Module $module): void
    {
        $this->modules[$module_config_name] = $module;
    }

    /**
     * @param string $module_config_name
     * @return Module
     */
    public function get(string $module_config_name): Module
    {
        return $this->modules[$module_config_name];
    }

    /**
     * @return array<string, Module>
     */
    public function all(): array
    {
        return $this->modules;
    }

    public function getDefault(): ?Module
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