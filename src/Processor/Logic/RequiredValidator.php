<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor\Logic;

use KaririCode\Validator\Processor\AbstractValidatorProcessor;

class RequiredValidator extends AbstractValidatorProcessor
{
    public function process(mixed $input): mixed
    {
        if ($this->isEmpty($input)) {
            $this->setInvalid('missingValue');
        }

        return $input;
    }

    private function isEmpty(mixed $value): bool
    {
        return null === $value
            || '' === $value
            || (is_string($value) && '' === trim($value))
            || (is_array($value) && 0 === count($value));
    }
}
