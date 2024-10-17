<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor\Logic;

use KaririCode\Validator\Processor\AbstractValidatorProcessor;

class RequiredValidator extends AbstractValidatorProcessor
{
    public function process(mixed $input): bool
    {
        if (is_string($input)) {
            return '' !== trim($input);
        }
        if (is_numeric($input)) {
            return true;
        }

        return null !== $input;
    }
}
