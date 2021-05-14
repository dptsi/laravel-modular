<?php


namespace Dptsi\Modular\Base;


abstract class BaseModule
{
    protected array $module_config;
    protected bool $default;

    /**
     * @param array $module_config
     * @param bool $is_default
     */
    public function __construct(array $module_config, bool $is_default)
    {
        $this->module_config = $module_config;
        $this->default = $is_default;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getConfig(string $key)
    {
        return $this->module_config[$key];
    }

    /**
     * @return string[]
     */
    public function getProviders(): array
    {
        return [];
    }

    public function isDefault(): bool
    {
        return $this->default;
    }
}