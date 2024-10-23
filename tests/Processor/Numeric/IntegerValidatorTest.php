<?php

declare(strict_types=1);

namespace KaririCode\Validator\Tests\Processor\Numeric;

use KaririCode\Validator\Processor\Numeric\IntegerValidator;
use PHPUnit\Framework\TestCase;

final class IntegerValidatorTest extends TestCase
{
    private IntegerValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new IntegerValidator();
    }

    /**
     * @dataProvider validIntegerProvider
     */
    public function testValidIntegers(mixed $value): void
    {
        $this->validator->process($value);

        $this->assertTrue($this->validator->isValid());
        $this->assertEmpty($this->validator->getErrorKey());
    }

    /**
     * @dataProvider invalidIntegerProvider
     */
    public function testInvalidIntegers(mixed $value): void
    {
        $this->validator->process($value);

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('notAnInteger', $this->validator->getErrorKey());
    }

    public static function validIntegerProvider(): array
    {
        return [
            [42],
            ['42'],
            [0],
            ['0'],
            [-123],
            ['-123'],
        ];
    }

    public static function invalidIntegerProvider(): array
    {
        return [
            [3.14],
            ['3.14'],
            ['abc'],
            ['12.34'],
            [null],
            [[]],
            [false],
            [''],
        ];
    }
}
