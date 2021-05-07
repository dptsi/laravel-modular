<?php


namespace Dptsi\Modular\Facade;


use Illuminate\Support\Facades\Facade;

/**
 * Class Event
 * @package Dptsi\Modular\Facade
 * @method static void hold()
 * @method static void publish($event)
 * @method static void release()
 * @method static void reset()
 */
class Event extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'event';
    }
}