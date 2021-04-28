<?php


namespace Dptsi\Modular\Facade;


use Dptsi\Modular\Base\Module;

class Manager
{
    /**
     * @var array<string, Module>
     */
    private array $modules;

    public function register(string $module_name, Module $module): void
    {
        $this->modules[$module_name] = $module;
    }

    /**
     * @param string $module_name
     * @return Module
     */
    public function get(string $module_name): Module
    {
        return $this->modules[$module_name];
    }

    /**
     * @return array<string, Module>
     */
    public function all(): array {
        return $this->modules;
    }
}