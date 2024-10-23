<?php

declare(strict_types=1);

namespace KaririCode\Validator\Tests\Processor\Date;

use KaririCode\Validator\Processor\Date\DateFormatValidator;
use PHPUnit\Framework\TestCase;

final class DateFormatValidatorTest extends TestCase
{
    private DateFormatValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new DateFormatValidator();
    }

    public function testDefaultFormatValidation(): void
    {
        $input = '2024-03-15';
        $this->validator->process($input);

        $this->assertTrue($this->validator->isValid());
        $this->assertEmpty($this->validator->getErrorKey());
    }

    public function testCustomFormatValidation(): void
    {
        $this->validator->configure(['format' => 'd/m/Y']);
        $input = '15/03/2024';

        $this->validator->process($input);

        $this->assertTrue($this->validator->isValid());
        $this->assertEmpty($this->validator->getErrorKey());
    }

    public function testInvalidFormat(): void
    {
        $input = '15-03-2024';
        $this->validator->process($input);

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('invalidFormat', $this->validator->getErrorKey());
    }

    public function testInvalidType(): void
    {
        $input = 123;
        $this->validator->process($input);

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('invalidType', $this->validator->getErrorKey());
    }
}
