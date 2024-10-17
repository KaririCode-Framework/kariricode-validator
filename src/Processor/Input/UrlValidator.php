<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor\Input;

use KaririCode\Validator\Processor\AbstractValidatorProcessor;

class UrlValidator extends AbstractValidatorProcessor
{
    public function process(mixed $input): bool
    {
        $input = $this->guardAgainstNonString($input);

        return false !== filter_var($input, FILTER_VALIDATE_URL);
    }
}
