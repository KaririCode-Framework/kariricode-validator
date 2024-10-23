<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor\Date;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Validator\Exception\DateValidatorException;
use KaririCode\Validator\Exception\MissingProcessorConfigException;
use KaririCode\Validator\Processor\AbstractValidatorProcessor;

class DateRangeValidator extends AbstractValidatorProcessor implements ConfigurableProcessor
{
    private \DateTimeInterface $minDate;
    private \DateTimeInterface $maxDate;
    private string $format = 'Y-m-d';

    public function configure(array $options): void
    {
        if (!isset($options['minDate'])) {
            throw MissingProcessorConfigException::missingConfiguration('DateRangeValidator', 'minDate');
        }

        if (!isset($options['maxDate'])) {
            throw MissingProcessorConfigException::missingConfiguration('DateRangeValidator', 'maxDate');
        }

        if (isset($options['format'])) {
            $this->format = $options['format'];
        }

        $this->minDate = self::parseDate($options['minDate'], $this->format);
        $this->maxDate = self::parseDate($options['maxDate'], $this->format);

        if ($this->minDate > $this->maxDate) {
            throw MissingProcessorConfigException::missingConfiguration('DateRangeValidator', 'minDate and maxDate order');
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

    private static function parseDate(string $date, string $format): \DateTimeInterface
    {
        $parsedDate = \DateTime::createFromFormat($format, $date);
        if (!$parsedDate || $parsedDate->format($format) !== $date) {
            throw DateValidatorException::invalidDateFormat($format, $date);
        }

        return $parsedDate;
    }
}
