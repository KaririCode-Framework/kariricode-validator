<?php

declare(strict_types=1);

namespace KaririCode\Validator\Tests\Processor\Numeric;

use KaririCode\Validator\Processor\Numeric\RangeValidator;
use PHPUnit\Framework\TestCase;

final class RangeValidatorTest extends TestCase
{
    private RangeValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new RangeValidator();
    }

    public function testDefaultRange(): void
    {
        $this->validator->configure([]);
        $input = 42;

        $this->validator->process($input);

        $this->assertTrue($this->validator->isValid());
        $this->assertEmpty($this->validator->getErrorKey());
    }

    public function testCustomRange(): void
    {
        $this->validator->configure(['min' => 1, 'max' => 100]);
        $input = 42;

        $this->validator->process($input);

        $this->assertTrue($this->validator->isValid());
        $this->assertEmpty($this->validator->getErrorKey());
    }

    public function testBelowRange(): void
    {
        $this->validator->configure(['min' => 10, 'max' => 20]);
        $input = 5;

        $this->validator->process($input);

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('outOfRange', $this->validator->getErrorKey());
    }

    public function testAboveRange(): void
    {
        $this->validator->configure(['min' => 10, 'max' => 20]);
        $input = 25;

        $this->validator->process($input);

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('outOfRange', $this->validator->getErrorKey());
    }

    public function testNonNumericInput(): void
    {
        $this->validator->configure(['min' => 1, 'max' => 100]);

        $this->validator->process('abc');

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('notNumeric', $this->validator->getErrorKey());
    }

    public function testStringNumericInput(): void
    {
        $this->validator->configure(['min' => 1, 'max' => 100]);
        $input = '42';

        $this->validator->process($input);

        $this->assertTrue($this->validator->isValid());
        $this->assertEmpty($this->validator->getErrorKey());
    }
}
