<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor\Logic;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Validator\Processor\AbstractValidatorProcessor;

class ConditionalValidator extends AbstractValidatorProcessor implements ConfigurableProcessor
{
    private \Closure $condition;
    private \Closure $validator;

    public function configure(array $options): void
    {
        if (!isset($options['condition']) || !($options['condition'] instanceof \Closure)) {
            throw new \InvalidArgumentException('A valid condition closure must be provided.');
        }
        if (!isset($options['validator']) || !($options['validator'] instanceof \Closure)) {
            throw new \InvalidArgumentException('A valid validator closure must be provided.');
        }
        $this->condition = $options['condition'];
        $this->validator = $options['validator'];
    }

    public function process(mixed $input): bool
    {
        if (($this->condition)($input)) {
            return ($this->validator)($input);
        }

        return true;
    }
}
