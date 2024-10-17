<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor\Date;

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
            throw new \InvalidArgumentException('Both "minDate" and "maxDate" options must be set.');
        }

        $this->setDate('minDate', $options['minDate']);
        $this->setDate('maxDate', $options['maxDate']);

        if (isset($options['format'])) {
            $this->format = $options['format'];
        }

        if ($this->minDate > $this->maxDate) {
            throw new \InvalidArgumentException('minDate cannot be greater than maxDate.');
        }
    }

    public function process(mixed $input): bool
    {
        var_dump($input);
        $input = $this->guardAgainstNonString($input);

        $date = \DateTime::createFromFormat($this->format, $input);
        if (!$date) {
            return false;
        }

        $date->setTime(0, 0, 0);

        return $date >= $this->minDate && $date <= $this->maxDate;
    }

    private function setDate(string $type, string $dateString): void
    {
        $date = \DateTime::createFromFormat($this->format, $dateString);
        if (!$date) {
            throw new \InvalidArgumentException(sprintf('Invalid %s format. Expected format: %s', $type, $this->format));
        }
        $date->setTime(0, 0, 0);
        $this->{$type} = $date;
    }
}
