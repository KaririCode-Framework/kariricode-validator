<?php

declare(strict_types=1);

namespace KaririCode\Validator\Contract;

interface ValidationResult
{
    public function isValid(): bool;

    public function getErrors(): array;

    public function getValidatedData(): array;

    public function toArray(): array;
}
