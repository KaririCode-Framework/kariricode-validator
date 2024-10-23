<?php

declare(strict_types=1);

namespace KaririCode\Validator\Exception;

use KaririCode\Exception\AbstractException;

final class MissingProcessorConfigException extends AbstractException
{
    private const CODE_MISSING_CONFIG = 4001;
    private const ERROR_CODE = 'MISSING_CONFIG';

    public static function missingConfiguration(string $processorName, string $requiredParameter): self
    {
        $message = sprintf(
            "Processor '%s' requires the parameter '%s', but it was not provided.",
            $processorName,
            $requiredParameter
        );

        return self::createException(self::CODE_MISSING_CONFIG, self::ERROR_CODE, $message);
    }
}
