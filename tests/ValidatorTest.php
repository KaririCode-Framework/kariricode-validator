<?php

declare(strict_types=1);

namespace KaririCode\Tests\Validator;

use KaririCode\Contract\Processor\ProcessorRegistry;
use KaririCode\Validator\Attribute\Validate;
use KaririCode\Validator\Processor\Input\EmailValidator;
use KaririCode\Validator\Processor\Logic\RequiredValidator;
use KaririCode\Validator\ValidationResult;
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

        $this->registry->method('get')
            ->willReturnMap([
                ['validator', 'required', new RequiredValidator()],
                ['validator', 'email', new EmailValidator()],
            ]);

        $this->validator = new Validator($this->registry);
    }

    public function testValidateWithValidObject(): void
    {
        $testObject = new class {
            #[Validate(processors: ['required', 'email'])]
            public string $email = 'walmir.silva@example.com';
        };

        $expectedResult = new ValidationResult();
        $expectedResult->setValidatedData('email', 'walmir.silva@example.com');

        $result = $this->validator->validate($testObject);

        $this->assertFalse($result->hasErrors());
        $this->assertEquals(['email' => 'walmir.silva@example.com'], $result->getValidatedData());
    }

    public function testValidateWithInvalidObject(): void
    {
        $testObject = new class {
            #[Validate(processors: ['required', 'email'])]
            public string $email = 'invalid-email';
        };

        $resultWithErrors = new ValidationResult();
        $resultWithErrors->addError('email', 'invalidFormat', 'Invalid email format');

        $result = $this->validator->validate($testObject);

        $this->assertTrue($result->hasErrors());
        $this->assertArrayHasKey('email', $result->getErrors());
    }

    public function testValidateWithNoAttributes(): void
    {
        $testObject = new class {
            public string $name = 'Test';
        };

        $result = $this->validator->validate($testObject);

        $this->assertFalse($result->hasErrors());
        $this->assertEmpty($result->getValidatedData());
    }

    public function testValidateWithNullObject(): void
    {
        $testObject = new \stdClass();

        $result = $this->validator->validate($testObject);

        $this->assertFalse($result->hasErrors());
        $this->assertEmpty($result->getValidatedData());
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

        $multiPropertyResult = new ValidationResult();
        $multiPropertyResult->setValidatedData('name', 'Walmir');
        $multiPropertyResult->setValidatedData('email', 'walmir.silva@example.com');

        $result = $this->validator->validate($testObject);

        $this->assertFalse($result->hasErrors());
        $this->assertEquals([
            'name' => 'Walmir',
            'email' => 'walmir.silva@example.com',
        ], $result->getValidatedData());
        $this->assertArrayNotHasKey('unvalidated', $result->getValidatedData());
    }

    public function testConstructorWithDefaultResultProcessor(): void
    {
        $validator = new Validator($this->registry);

        $testObject = new class {
            #[Validate(processors: ['required'])]
            public string $name = 'Test';
        };

        $result = $validator->validate($testObject);

        $this->assertInstanceOf(ValidationResult::class, $result);
    }

    public function testValidateWithNonObject(): void
    {
        $this->expectException(\TypeError::class);
        $this->validator->validate('not an object');
    }
}
