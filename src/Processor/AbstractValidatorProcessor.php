<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor;

use KaririCode\Contract\Processor\Processor;
use KaririCode\Validator\Exception\ValidationException;

abstract class AbstractValidatorProcessor implements Processor
{
    protected function guardAgainstNonString(mixed $input): string
    {
        if (!is_string($input)) {
            throw new ValidationException('Input must be a string');
        }

        return $input;
    }

    abstract public function process(mixed $input): bool;
}
