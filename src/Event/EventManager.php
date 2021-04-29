<?php


namespace Dptsi\Modular\Event;


class EventManager
{
    private array $events = [];
    private bool $on_hold = false;

    public function hold()
    {
        $this->on_hold = true;
    }

    /**
     * Directly publish domain event.
     * @param $event
     */
    public function publish($event)
    {
        if ($this->on_hold) {
            $this->events[] = $event;
        } else {
            event($event);
        }
    }

    public function release()
    {
        $this->on_hold = false;

        while (!empty($this->events)) {
            $event = array_shift($this->events);
            event($event);
        }
    }

    public function reset()
    {
        $this->events = [];
    }
}