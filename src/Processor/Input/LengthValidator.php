<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor\Input;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Validator\Processor\AbstractValidatorProcessor;

class LengthValidator extends AbstractValidatorProcessor implements ConfigurableProcessor
{
    private int $minLength = 0;
    private int $maxLength = PHP_INT_MAX;

    public function configure(array $options): void
    {
        if (isset($options['minLength'])) {
            $this->minLength = $options['minLength'];
        }
        if (isset($options['maxLength'])) {
            $this->maxLength = $options['maxLength'];
        }
    }

    public function process(mixed $input): bool
    {
        $input = $this->guardAgainstNonString($input);
        $length = mb_strlen($input);

        return $length >= $this->minLength && $length <= $this->maxLength;
    }
}
