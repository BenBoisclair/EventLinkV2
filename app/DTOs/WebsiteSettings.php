<?php

namespace App\DTOs;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class WebsiteSettings implements Arrayable, JsonSerializable
{
    public object $metadata;

    public function __construct(array $attributes = [])
    {
        $this->metadata = (object) [
            'title' => $attributes['metadata']['title'] ?? '',
            'description' => $attributes['metadata']['description'] ?? '',
        ];
    }

    public static function fromDefaults($event): self
    {
        return new self([
            'metadata' => [
                'title' => $event->name ?? '',
                'description' => $event->description ?? '',
            ]
        ]);
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            'metadata' => [
                'title' => $this->metadata->title ?? '',
                'description' => $this->metadata->description ?? '',
            ]
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
