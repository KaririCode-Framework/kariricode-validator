<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor\Numeric;

use KaririCode\Validator\Processor\AbstractValidatorProcessor;

class IntegerValidator extends AbstractValidatorProcessor
{
    public function process(mixed $input): mixed
    {
        if (!is_int($input) && (!is_string($input) || !ctype_digit($input))) {
            $this->setInvalid('notAnInteger');
        }

        return $input;
    }
}
