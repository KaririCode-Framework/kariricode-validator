<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor\Numeric;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Validator\Processor\AbstractValidatorProcessor;

class RangeValidator extends AbstractValidatorProcessor implements ConfigurableProcessor
{
    private float $min;
    private float $max;

    public function configure(array $options): void
    {
        $this->min = $options['min'] ?? PHP_FLOAT_MIN;
        $this->max = $options['max'] ?? PHP_FLOAT_MAX;
    }

    public function process(mixed $input): mixed
    {
        if (!is_numeric($input)) {
            $this->setInvalid('notNumeric');

            return $input;
        }

        $value = (float) $input;

        if ($value < $this->min || $value > $this->max) {
            $this->setInvalid('outOfRange');
        }

        return $value;
    }
}
