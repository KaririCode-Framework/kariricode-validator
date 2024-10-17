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
        if (!isset($options['min']) || !isset($options['max'])) {
            throw new \InvalidArgumentException('Both "min" and "max" options must be set.');
        }
        $this->min = $options['min'];
        $this->max = $options['max'];
    }

    public function process(mixed $input): bool
    {
        var_dump($input);
        $value = (float) $input;

        return $value >= $this->min && $value <= $this->max;
    }
}
