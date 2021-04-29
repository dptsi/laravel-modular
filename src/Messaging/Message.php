<?php


namespace Dptsi\Modular\Messaging;


use Ramsey\Uuid\Uuid;

class Message
{
    private string $identity;
    private string $label;
    /**
     * @var array<string, mixed>
     */
    private array $content;
    private string $source_channel;

    /**
     * @param string $source_channel
     * @param string $label
     * @param array<string, mixed> $content
     */
    public function __construct(string $source_channel, string $label, array $content)
    {
        $this->identity = Uuid::uuid4()->toString();
        $this->label = $label;
        $this->content = $content;
        $this->source_channel = $source_channel;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }

    public function getSourceChannel(): string
    {
        return $this->source_channel;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return array<string, mixed>
     */
    public function getContent(): array
    {
        return $this->content;
    }
}