<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor\Numeric;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Validator\Processor\AbstractValidatorProcessor;

class DateRangeValidator extends AbstractValidatorProcessor implements ConfigurableProcessor
{
    private \DateTimeInterface $minDate;
    private \DateTimeInterface $maxDate;
    private string $format = 'Y-m-d';

    public function configure(array $options): void
    {
        if (!isset($options['minDate']) || !isset($options['maxDate'])) {
            throw new \InvalidArgumentException('Both minDate and maxDate must be provided');
        }

        $this->minDate = $this->parseDate($options['minDate']);
        $this->maxDate = $this->parseDate($options['maxDate']);

        if ($this->minDate > $this->maxDate) {
            throw new \InvalidArgumentException('minDate must be less than or equal to maxDate');
        }

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
            $this->setInvalid('invalidDate');

            return $input;
        }

        if ($date < $this->minDate || $date > $this->maxDate) {
            $this->setInvalid('outOfRange');
        }

        return $input;
    }

    private function parseDate(string $date): \DateTimeInterface
    {
        $parsedDate = \DateTime::createFromFormat($this->format, $date);
        if (!$parsedDate) {
            throw new \InvalidArgumentException("Invalid date format. Expected: {$this->format}");
        }

        return $parsedDate;
    }
}
