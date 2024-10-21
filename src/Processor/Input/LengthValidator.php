<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor\Input;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Validator\Exception\MissingProcessorConfigException;
use KaririCode\Validator\Processor\AbstractValidatorProcessor;

class LengthValidator extends AbstractValidatorProcessor implements ConfigurableProcessor
{
    private int $minLength = 0;
    private int $maxLength = PHP_INT_MAX;

    public function configure(array $options): void
    {
        if (empty($options)) {
            throw MissingProcessorConfigException::missingConfiguration('LengthValidator', 'Configuration is empty');
        }

        if (!isset($options['minLength'])) {
            throw MissingProcessorConfigException::missingConfiguration('LengthValidator', 'minLength');
        }

        if (!isset($options['maxLength'])) {
            throw MissingProcessorConfigException::missingConfiguration('LengthValidator', 'maxLength');
        }

        $this->minLength = $options['minLength'];
        $this->maxLength = $options['maxLength'];
    }

    public function process(mixed $input): mixed
    {
        if (!is_string($input)) {
            $this->setInvalid('invalidType');

            return $input;
        }

        $length = mb_strlen($input);

        if ($length < $this->minLength) {
            $this->setInvalid('tooShort');
        } elseif ($length > $this->maxLength) {
            $this->setInvalid('tooLong');
        }

        return $input;
    }
}
