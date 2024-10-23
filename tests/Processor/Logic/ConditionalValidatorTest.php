<?php

declare(strict_types=1);

namespace KaririCode\Validator\Tests\Processor\Logic;

use KaririCode\Validator\Processor\Logic\ConditionalValidator;
use PHPUnit\Framework\TestCase;

final class ConditionalValidatorTest extends TestCase
{
    private ConditionalValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new ConditionalValidator();
    }

    public function testValidCondition(): void
    {
        $this->validator->configure([
            'condition' => fn ($input) => $input > 0,
            'validator' => fn ($input) => $input < 10
        ]);

        $this->validator->process(5);

        $this->assertTrue($this->validator->isValid());
        $this->assertEmpty($this->validator->getErrorKey());
    }

    public function testInvalidCondition(): void
    {
        $this->validator->configure([
            'condition' => fn ($input) => $input > 0,
            'validator' => fn ($input) => $input < 10
        ]);

        $this->validator->process(15);

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('conditionNotMet', $this->validator->getErrorKey());
    }

    public function testConditionNotMet(): void
    {
        $this->validator->configure([
            'condition' => fn ($input) => $input > 100,
            'validator' => fn ($input) => $input < 10
        ]);

        $this->validator->process(50);

        $this->assertTrue($this->validator->isValid());
        $this->assertEmpty($this->validator->getErrorKey());
    }

    public function testMissingConditionThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->configure([
            'validator' => fn ($input) => true
        ]);
    }

    public function testMissingValidatorThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->configure([
            'condition' => fn ($input) => true
        ]);
    }
}
