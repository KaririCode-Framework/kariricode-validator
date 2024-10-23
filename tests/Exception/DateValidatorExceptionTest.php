<?php

declare(strict_types=1);

namespace KaririCode\Tests\Validator\Exception;

use KaririCode\Exception\AbstractException;
use KaririCode\Validator\Exception\DateValidatorException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \KaririCode\Validator\Exception\DateValidatorException
 */
class DateValidatorExceptionTest extends TestCase
{
    public function testInstanceOf(): void
    {
        $exception = DateValidatorException::invalidDateFormat('Y-m-d', '31/12/2023');

        $this->assertInstanceOf(AbstractException::class, $exception);
        $this->assertInstanceOf(DateValidatorException::class, $exception);
    }

    public function testGetErrorCode(): void
    {
        $exception = DateValidatorException::invalidDateFormat('Y-m-d', '31/12/2023');

        $this->assertEquals('INVALID_DATE_FORMAT', $exception->getErrorCode());
    }

    public function testGetCode(): void
    {
        $exception = DateValidatorException::invalidDateFormat('Y-m-d', '31/12/2023');

        $this->assertEquals(4002, $exception->getCode());
    }

    public function testInvalidDateFormatException(): void
    {
        $expectedFormat = 'Y-m-d';
        $providedDate = '31/12/2023';

        $exception = DateValidatorException::invalidDateFormat($expectedFormat, $providedDate);

        $this->assertInstanceOf(DateValidatorException::class, $exception);
        $this->assertEquals(
            "Invalid date format. Expected: 'Y-m-d', but got: '31/12/2023'.",
            $exception->getMessage()
        );
        $this->assertEquals(4002, $exception->getCode());
        $this->assertEquals('INVALID_DATE_FORMAT', $exception->getErrorCode());
    }

    /**
     * @dataProvider provideDifferentDateFormats
     */
    public function testInvalidDateFormatWithDifferentFormats(
        string $format,
        string $date,
        string $expectedMessage
    ): void {
        $exception = DateValidatorException::invalidDateFormat($format, $date);

        $this->assertEquals($expectedMessage, $exception->getMessage());
        $this->assertEquals(4002, $exception->getCode());
        $this->assertEquals('INVALID_DATE_FORMAT', $exception->getErrorCode());
    }

    /**
     * @return array<string, array{format: string, date: string, expectedMessage: string}>
     */
    public static function provideDifferentDateFormats(): array
    {
        return [
            'DMY format' => [
                'format' => 'd/m/Y',
                'date' => '2023-12-31',
                'expectedMessage' => "Invalid date format. Expected: 'd/m/Y', but got: '2023-12-31'.",
            ],
            'YMD with dots' => [
                'format' => 'Y.m.d',
                'date' => '31-12-2023',
                'expectedMessage' => "Invalid date format. Expected: 'Y.m.d', but got: '31-12-2023'.",
            ],
            'MDY format' => [
                'format' => 'm-d-Y',
                'date' => '2023/12/31',
                'expectedMessage' => "Invalid date format. Expected: 'm-d-Y', but got: '2023/12/31'.",
            ],
        ];
    }

    public function testExceptionWithSpecialCharacters(): void
    {
        $expectedFormat = 'Y-m-d';
        $providedDate = "2023-12-31'--\"";

        $exception = DateValidatorException::invalidDateFormat($expectedFormat, $providedDate);

        $message = $exception->getMessage();

        $this->assertStringContainsString("Expected: 'Y-m-d'", $message);
        $this->assertStringContainsString("2023-12-31'--\"", $message);
        $this->assertStringStartsWith('Invalid date format.', $message);
        $this->assertEquals(4002, $exception->getCode());
        $this->assertEquals('INVALID_DATE_FORMAT', $exception->getErrorCode());
    }

    /**
     * @dataProvider provideEmptyValues
     */
    public function testExceptionWithEmptyValues(
        string $format,
        string $date,
        string $expectedMessage
    ): void {
        $exception = DateValidatorException::invalidDateFormat($format, $date);

        $this->assertEquals($expectedMessage, $exception->getMessage());
        $this->assertEquals(4002, $exception->getCode());
        $this->assertEquals('INVALID_DATE_FORMAT', $exception->getErrorCode());
    }

    /**
     * @return array<string, array{format: string, date: string, expectedMessage: string}>
     */
    public static function provideEmptyValues(): array
    {
        return [
            'empty format' => [
                'format' => '',
                'date' => '2023-12-31',
                'expectedMessage' => "Invalid date format. Expected: '', but got: '2023-12-31'.",
            ],
            'empty date' => [
                'format' => 'Y-m-d',
                'date' => '',
                'expectedMessage' => "Invalid date format. Expected: 'Y-m-d', but got: ''.",
            ],
            'both empty' => [
                'format' => '',
                'date' => '',
                'expectedMessage' => "Invalid date format. Expected: '', but got: ''.",
            ],
        ];
    }

    public function testExceptionConstantsValues(): void
    {
        $reflection = new \ReflectionClass(DateValidatorException::class);

        $codeConstant = $reflection->getConstant('CODE_INVALID_FORMAT');
        $errorCodeConstant = $reflection->getConstant('ERROR_CODE');

        $exception = DateValidatorException::invalidDateFormat('Y-m-d', '2023-12-31');

        $this->assertEquals($codeConstant, $exception->getCode());
        $this->assertEquals($errorCodeConstant, $exception->getErrorCode());

        $this->assertEquals(4002, $codeConstant);
        $this->assertEquals('INVALID_DATE_FORMAT', $errorCodeConstant);
    }

    /**
     * @dataProvider provideUnicodeCharacters
     */
    public function testExceptionWithUnicodeCharacters(string $date): void
    {
        $expectedFormat = 'Y-m-d';

        $exception = DateValidatorException::invalidDateFormat($expectedFormat, $date);

        $message = $exception->getMessage();

        $this->assertStringContainsString("Expected: 'Y-m-d'", $message);
        $this->assertStringContainsString($date, $message);
        $this->assertStringStartsWith('Invalid date format.', $message);
        $this->assertEquals(4002, $exception->getCode());
        $this->assertEquals('INVALID_DATE_FORMAT', $exception->getErrorCode());
    }

    /**
     * @return array<string, array{date: string}>
     */
    public static function provideUnicodeCharacters(): array
    {
        return [
            'emojis' => ['date' => '2023-12-31😀😎🎉'],
            'special symbols' => ['date' => '2023-12-31★☺♠'],
            'accents' => ['date' => '2023-12-31éàêç'],
            'mixed characters' => ['date' => '2023-12-31★🎉çé'],
        ];
    }
}
