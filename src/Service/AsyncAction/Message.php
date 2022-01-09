<?php

declare(strict_types=1);

namespace App\Service\AsyncAction;

use App\Enum\KafkaTopic;
use DateTimeInterface;

final class Message
{
    private string $key;
    private KafkaTopic $topic;
    private string $type;
    private string $name;
    private string $createdAt;

    public function __construct(
        string     $key,
        KafkaTopic $topic,
        string     $type,
        string     $name,
        string     $createdAt = null
    ){
        $this->key = $key;
        $this->topic = $topic;
        $this->type = $type;
        $this->name = $name;

        if (null !== $createdAt) {
            $this->createdAt = $createdAt;
        } else {
            $this->createdAt = (new \DateTime())->format(DateTimeInterface::ATOM);
        }
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getTopic(): KafkaTopic
    {
        return $this->topic;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function toJson(): string
    {
        return json_encode([
            'type' => $this->type,
            'name' => $this->name,
            'createdAt' => $this->createdAt,
            'payload' => [
                'data' => $this->key
            ],
            'metadata' => [
                'key' => $this->key,
                'topic' => $this->topic->value
            ]
        ]);
    }
}
