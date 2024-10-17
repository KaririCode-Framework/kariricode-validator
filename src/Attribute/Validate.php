<?php

declare(strict_types=1);

namespace KaririCode\Validator\Attribute;

use KaririCode\Contract\Processor\ProcessableAttribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Validate implements ProcessableAttribute
{
    public function __construct(
        public readonly array $validators,
        public readonly ?string $fallbackValue = null
    ) {
    }

    public function getProcessors(): array
    {
        return $this->validators;
    }

    public function getFallbackValue(): mixed
    {
        return $this->fallbackValue;
    }
}
