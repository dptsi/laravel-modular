<?php


namespace Dptsi\Modular\Base;


abstract class Module
{
    /**
     * @return string[]
     */
    public function getProviders(): array
    {
        return [];
    }

    /**
     * @return array{?string, ?string}
     */
    public function getDefaultControllerAction(): array
    {
        return [null, null];
    }
}