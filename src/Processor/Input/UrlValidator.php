<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor\Input;

use KaririCode\Validator\Processor\AbstractValidatorProcessor;

class UrlValidator extends AbstractValidatorProcessor
{
    public function process(mixed $input): mixed
    {
        if (!is_string($input)) {
            $this->setInvalid('invalidType');

            return $input;
        }

        if (false === filter_var($input, FILTER_VALIDATE_URL)) {
            $this->setInvalid('invalidFormat');
        }

        return $input;
    }
}
