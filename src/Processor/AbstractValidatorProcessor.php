<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor;

use KaririCode\Contract\Processor\Processor;
use KaririCode\Contract\Processor\ValidatableProcessor;

abstract class AbstractValidatorProcessor implements Processor, ValidatableProcessor
{
    protected bool $isValid = true;
    protected string $errorKey = '';

    /**
     * Reset the processor's state back to its initial values.
     */
    public function reset(): void
    {
        $this->isValid = true;
        $this->errorKey = '';
    }

    protected function setInvalid(string $errorKey): void
    {
        $this->isValid = false;
        $this->errorKey = $errorKey;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getErrorKey(): string
    {
        return $this->errorKey;
    }

    abstract public function process(mixed $input): mixed;
}
