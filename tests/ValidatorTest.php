<?php

declare(strict_types=1);

namespace KaririCode\Validator\Tests;

use KaririCode\Contract\Processor\Processor;
use KaririCode\Contract\Processor\ProcessorRegistry;
use KaririCode\Validator\Attribute\Validate;
use KaririCode\Validator\Result\ValidationResult;
use KaririCode\Validator\Validator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    private ProcessorRegistry|MockObject $registry;
    private Validator $validator;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ProcessorRegistry::class);
        $this->validator = new Validator($this->registry);
    }

    public function testValidateWithValidObject(): void
    {
        $testObject = new class {
            #[Validate(processors: ['required', 'email'])]
            public string $email = 'walmir.silva@example.com';
        };

        $this->registry->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnMap([
                ['validator', 'required', $this->createMock(Processor::class)],
                ['validator', 'email', $this->createMock(Processor::class)],
            ]);

        $result = $this->validator->validate($testObject);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue($result->isValid());
    }

    public function testValidateWithInvalidObject(): void
    {
        $testObject = new class {
            #[Validate(processors: ['required', 'email'])]
            public string $email = 'invalid-email';
        };

        $processor = $this->createMock(Processor::class);
        $processor->method('process')
            ->with($this->anything())
            ->willReturn('invalid-email');

        $this->registry->method('get')
            ->willReturn($processor);

        $result = $this->validator->validate($testObject);

        $this->assertInstanceOf(ValidationResult::class, $result);

        $this->assertFalse(!$result->isValid());
    }

    public function testValidateWithNoAttributes(): void
    {
        $testObject = new class {
            public string $name = 'Test';
        };

        $result = $this->validator->validate($testObject);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue($result->isValid());
    }

    public function testValidateWithNullObject(): void
    {
        $testObject = new \stdClass();

        $result = $this->validator->validate($testObject);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue($result->isValid());
    }

    public function testValidateWithMultipleProperties(): void
    {
        $testObject = new class {
            #[Validate(processors: ['required'])]
            public string $name = 'Walmir';

            #[Validate(processors: ['required'])]
            public string $email = 'walmir.silva@example.com';

            public string $unvalidated = 'Skip this';
        };

        $this->registry->expects($this->atLeastOnce())
            ->method('get')
            ->with('validator', 'required')
            ->willReturn($this->createMock(Processor::class));

        $result = $this->validator->validate($testObject);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue($result->isValid());
    }

    public function testValidateWithNonObject(): void
    {
        $this->expectException(\TypeError::class);
        $this->validator->validate('not an object');
    }
}
