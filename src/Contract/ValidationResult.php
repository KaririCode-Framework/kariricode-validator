<?php

declare(strict_types=1);

namespace KaririCode\Validator\Contract;

interface ValidationResult
{
    public function addError(string $property, string $errorKey, string $message): void;

    public function setValidatedData(string $property, mixed $value): void;

    public function hasErrors(): bool;

    public function getErrors(): array;

    public function getValidatedData(): array;

    public function toArray(): array;
}
