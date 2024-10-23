<?php

declare(strict_types=1);

namespace KaririCode\Validator\Tests\Processor\Logic;

use KaririCode\Validator\Processor\Logic\RequiredValidator;
use PHPUnit\Framework\TestCase;

final class RequiredValidatorTest extends TestCase
{
    private RequiredValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new RequiredValidator();
    }

    /**
     * @dataProvider validValuesProvider
     */
    public function testValidValues(mixed $value): void
    {
        $this->validator->process($value);

        $this->assertTrue($this->validator->isValid());
        $this->assertEmpty($this->validator->getErrorKey());
    }

    /**
     * @dataProvider emptyValuesProvider
     */
    public function testEmptyValues(mixed $value): void
    {
        $this->validator->process($value);

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('missingValue', $this->validator->getErrorKey());
    }

    public static function validValuesProvider(): array
    {
        return [
            ['test'],
            [123],
            [0],
            [false],
            [['item']],
            [' test '],
        ];
    }

    public static function emptyValuesProvider(): array
    {
        return [
            [null],
            [''],
            [' '],
            [[]],
        ];
    }
}
