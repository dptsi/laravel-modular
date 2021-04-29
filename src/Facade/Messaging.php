<?php


namespace Dptsi\Modular\Facade;


use Closure;
use Dptsi\Modular\Messaging\Message;
use Illuminate\Support\Facades\Facade;

/**
 * Class Messaging
 * @package Dptsi\Modular\Facade
 * @method static void setChannel(string $channel)
 * @method static void listenTo(string $channel, Closure $on_message_received)
 * @method static void clearListeners()
 * @method static void broadcast(string $source_channel, string $label, array $message)
 * @method static void disableListenersExceptOn(string $channel)
 * @method static void enableListeners()
 * @method static void process(Message $message)
 */
class Messaging extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'message_bus';
    }
}