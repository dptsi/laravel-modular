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
}