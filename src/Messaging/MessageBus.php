<?php


namespace Dptsi\Modular\Messaging;


use Closure;
use Illuminate\Support\Facades\DB;

class MessageBus
{
    private array $listeners = [];
    private string $current_channel = 'default';
    private ?string $allowed_listener_channel = null;

    public function setChannel(string $channel)
    {
        $this->current_channel = $channel;
    }

    public function listenTo(string $channel, Closure $on_message_received)
    {
        $this->listeners[$channel][$this->current_channel][] = $on_message_received;
    }

    /**
     * Clear all registered listeners
     */
    public function clearListeners()
    {
        $this->listeners = [];
    }

    /**
     * Broadcast message from source channel into queue.
     *
     * Message can be held by MessageBus::holdMessages() and reinserted into queue by MessageBus::releaseMessages() or discarded by MessageBus::resetMessage()
     *
     * @param string $source_channel
     * @param string $label
     * @param array $message
     */
    public function broadcast(string $source_channel, string $label, array $message)
    {
        $payload = new Message($source_channel, $label, $message);

        MessageBusJob::dispatch($payload);
    }

    public function disableListenersExceptOn(string $channel)
    {
        $this->allowed_listener_channel = $channel;
    }

    public function enableListeners()
    {
        $this->allowed_listener_channel = null;
    }

    /**
     * THIS IS NOT A PUBLIC INTERFACE
     * @param Message $message
     */
    public function process(Message $message)
    {
        if (!isset($this->listeners[$message->getSourceChannel()])) {
            return;
        }

        foreach ($this->listeners[$message->getSourceChannel()] as $processing_channel => $listeners) {
            if ($this->allowed_listener_channel !== null) {
                if ($processing_channel != $this->allowed_listener_channel) {
                    continue;
                }
            }

            if ($this->hasProcessedMessage($processing_channel, $message->getIdentity())) {
                continue;
            }

            foreach ($listeners as $listener) {
                $listener($message->getLabel(), $message->getContent());
            }

            $this->trackChannelProcessedMessage($processing_channel, $message->getIdentity());
        }
    }

    private function hasProcessedMessage(string $channel, string $identity): bool
    {
        return DB::table('message_identity_tracking')
            ->where('channel', $channel)
            ->where('identity', $identity)
            ->exists();
    }

    private function trackChannelProcessedMessage(string $channel, string $identity): void
    {
        DB::table('message_identity_tracking')->insert(
            [
                'channel' => $channel,
                'identity' => $identity,
                'processed_at' => now()->getTimestamp(),
            ]
        );
    }
}