<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor\Date;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Validator\Processor\AbstractValidatorProcessor;

class DateFormatValidator extends AbstractValidatorProcessor implements ConfigurableProcessor
{
    private string $format = 'Y-m-d';

    public function configure(array $options): void
    {
        if (isset($options['format'])) {
            $this->format = $options['format'];
        }
    }

    public function process(mixed $input): mixed
    {
        if (!is_string($input)) {
            $this->setInvalid('invalidType');

            return $input;
        }

        $date = \DateTime::createFromFormat($this->format, $input);

        if (!$date || $date->format($this->format) !== $input) {
            $this->setInvalid('invalidFormat');
        }

        return $input;
    }
}
