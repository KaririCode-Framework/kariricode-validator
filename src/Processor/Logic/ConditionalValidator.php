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
            throw new \InvalidArgumentException('Condition must be a Closure');
        }
        $this->condition = $options['condition'];

        if (!isset($options['validator']) || !($options['validator'] instanceof \Closure)) {
            throw new \InvalidArgumentException('Validator must be a Closure');
        }
        $this->validator = $options['validator'];
    }

    public function process(mixed $input): mixed
    {
        if (($this->condition)($input)) {
            $validationResult = ($this->validator)($input);
            if (true !== $validationResult) {
                $this->setInvalid('conditionNotMet');
            }
        }

        return $input;
    }
}
