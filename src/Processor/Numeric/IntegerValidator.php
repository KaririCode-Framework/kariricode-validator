<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor\Numeric;

use KaririCode\Validator\Processor\AbstractValidatorProcessor;

class IntegerValidator extends AbstractValidatorProcessor
{
    public function process(mixed $input): mixed
    {
        if (!$this->isValidInteger($input)) {
            $this->setInvalid('notAnInteger');
        }

        return $input;
    }

    private function isValidInteger(mixed $input): bool
    {
        return is_int($input) || (is_string($input) && false !== filter_var($input, FILTER_VALIDATE_INT));
    }
}
