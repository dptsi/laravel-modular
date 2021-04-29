<?php


namespace Dptsi\Modular\Messaging;


use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class MessageBusJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    private Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * @param Container $container
     * @throws BindingResolutionException
     */
    public function handle(Container $container)
    {
        /** @var MessageBus $bus */
        $bus = $container->make('message_bus');
        $bus->process($this->message);
    }
}