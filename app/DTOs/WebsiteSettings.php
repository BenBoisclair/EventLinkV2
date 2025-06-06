<?php

namespace App\DTOs;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class WebsiteSettings implements Arrayable, JsonSerializable
{
    public object $metadata;
    public object $theme;
    public object $styling;

    public function __construct(array $attributes = [])
    {
        $this->metadata = (object) [
            'title' => $attributes['metadata']['title'] ?? '',
            'description' => $attributes['metadata']['description'] ?? '',
        ];
        
        $this->theme = (object) [
            'primary' => $attributes['theme']['primary'] ?? '#3b82f6',
            'secondary' => $attributes['theme']['secondary'] ?? '#64748b',
            'accent' => $attributes['theme']['accent'] ?? '#f59e0b',
            'background' => $attributes['theme']['background'] ?? '#ffffff',
        ];
        
        $this->styling = (object) [
            'borderRadius' => $attributes['styling']['borderRadius'] ?? 'medium',
            'buttonSize' => $attributes['styling']['buttonSize'] ?? 'md',
            'shadow' => $attributes['styling']['shadow'] ?? 'sm',
            'buttonStyle' => $attributes['styling']['buttonStyle'] ?? 'solid',
            'animationSpeed' => $attributes['styling']['animationSpeed'] ?? 'normal',
            'fontWeight' => $attributes['styling']['fontWeight'] ?? 'medium',
            'letterSpacing' => $attributes['styling']['letterSpacing'] ?? 'normal',
        ];
    }

    public static function fromDefaults($event): self
    {
        return new self([
            'metadata' => [
                'title' => $event->name ?? '',
                'description' => $event->description ?? '',
            ],
            'theme' => [
                'primary' => '#3b82f6',
                'secondary' => '#64748b',
                'accent' => '#f59e0b',
                'background' => '#ffffff',
            ],
            'styling' => [
                'borderRadius' => 'medium',
                'buttonSize' => 'md',
                'shadow' => 'sm',
                'buttonStyle' => 'solid',
                'animationSpeed' => 'normal',
                'fontWeight' => 'medium',
                'letterSpacing' => 'normal',
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
            ],
            'theme' => [
                'primary' => $this->theme->primary ?? '#3b82f6',
                'secondary' => $this->theme->secondary ?? '#64748b',
                'accent' => $this->theme->accent ?? '#f59e0b',
                'background' => $this->theme->background ?? '#ffffff',
            ],
            'styling' => [
                'borderRadius' => $this->styling->borderRadius ?? 'medium',
                'buttonSize' => $this->styling->buttonSize ?? 'md',
                'shadow' => $this->styling->shadow ?? 'sm',
                'buttonStyle' => $this->styling->buttonStyle ?? 'solid',
                'animationSpeed' => $this->styling->animationSpeed ?? 'normal',
                'fontWeight' => $this->styling->fontWeight ?? 'medium',
                'letterSpacing' => $this->styling->letterSpacing ?? 'normal',
            ]
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
