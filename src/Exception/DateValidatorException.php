<?php

declare(strict_types=1);

namespace KaririCode\Validator\Exception;

use KaririCode\Exception\AbstractException;

final class DateValidatorException extends AbstractException
{
    private const CODE_INVALID_FORMAT = 4002;
    private const ERROR_CODE = 'INVALID_DATE_FORMAT';

    public static function invalidDateFormat(string $expectedFormat, string $providedDate): self
    {
        $message = sprintf(
            "Invalid date format. Expected: '%s', but got: '%s'.",
            $expectedFormat,
            $providedDate
        );

        return self::createException(self::CODE_INVALID_FORMAT, self::ERROR_CODE, $message);
    }
}
