<?php

declare(strict_types=1);

namespace KaririCode\Tests\Validator\Processor;

use KaririCode\PropertyInspector\AttributeHandler;
use KaririCode\Validator\Processor\DefaultValidationResultProcessor;
use KaririCode\Validator\ValidationResult;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DefaultValidationResultProcessorTest extends TestCase
{
    private AttributeHandler|MockObject $attributeHandler;
    private ValidationResult $validationResult;
    private DefaultValidationResultProcessor $processor;

    protected function setUp(): void
    {
        $this->attributeHandler = $this->createMock(AttributeHandler::class);
        $this->validationResult = new ValidationResult();
        $this->processor = new DefaultValidationResultProcessor($this->validationResult);
    }

    public function testProcessWithValidData(): void
    {
        $processedValues = [
            'name' => ['value' => 'Walmir Silva'],
            'email' => ['value' => 'walmir@example.com'],
        ];

        $this->attributeHandler
            ->expects($this->once())
            ->method('getProcessedPropertyValues')
            ->willReturn($processedValues);

        $this->attributeHandler
            ->expects($this->once())
            ->method('getProcessingResultErrors')
            ->willReturn([]);

        $result = $this->processor->process($this->attributeHandler);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertEquals(
            [
                'name' => 'Walmir Silva',
                'email' => 'walmir@example.com',
            ],
            $result->getValidatedData()
        );
        $this->assertFalse($result->hasErrors());
    }

    public function testProcessWithErrors(): void
    {
        $processedValues = [
            'email' => ['value' => 'invalid-email'],
        ];

        $errors = [
            'email' => [
                [
                    'errorKey' => 'invalidFormat',
                    'message' => 'Invalid email format',
                ],
            ],
        ];

        $this->attributeHandler
            ->expects($this->once())
            ->method('getProcessedPropertyValues')
            ->willReturn($processedValues);

        $this->attributeHandler
            ->expects($this->once())
            ->method('getProcessingResultErrors')
            ->willReturn($errors);

        $result = $this->processor->process($this->attributeHandler);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertEquals(['email' => 'invalid-email'], $result->getValidatedData());
        $this->assertTrue($result->hasErrors());
        $this->assertArrayHasKey('email', $result->getErrors());
        $this->assertEquals('invalidFormat', $result->getErrors()['email'][0]['errorKey']);
        $this->assertEquals('Invalid email format', $result->getErrors()['email'][0]['message']);
    }

    public function testProcessWithMultipleErrorsForSameProperty(): void
    {
        $processedValues = [
            'password' => ['value' => 'weak'],
        ];

        $errors = [
            'password' => [
                [
                    'errorKey' => 'tooShort',
                    'message' => 'Password is too short',
                ],
                [
                    'errorKey' => 'complexity',
                    'message' => 'Password needs special characters',
                ],
            ],
        ];

        $this->attributeHandler
            ->expects($this->once())
            ->method('getProcessedPropertyValues')
            ->willReturn($processedValues);

        $this->attributeHandler
            ->expects($this->once())
            ->method('getProcessingResultErrors')
            ->willReturn($errors);

        $result = $this->processor->process($this->attributeHandler);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertEquals(['password' => 'weak'], $result->getValidatedData());
        $this->assertTrue($result->hasErrors());

        $resultErrors = $result->getErrors()['password'];
        $this->assertCount(2, $resultErrors);
        $this->assertEquals('tooShort', $resultErrors[0]['errorKey']);
        $this->assertEquals('Password is too short', $resultErrors[0]['message']);
        $this->assertEquals('complexity', $resultErrors[1]['errorKey']);
        $this->assertEquals('Password needs special characters', $resultErrors[1]['message']);
    }

    public function testProcessWithNoProperties(): void
    {
        $this->attributeHandler
            ->expects($this->once())
            ->method('getProcessedPropertyValues')
            ->willReturn([]);

        $this->attributeHandler
            ->expects($this->once())
            ->method('getProcessingResultErrors')
            ->willReturn([]);

        $result = $this->processor->process($this->attributeHandler);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertEmpty($result->getValidatedData());
        $this->assertFalse($result->hasErrors());
    }

    public function testProcessWithMixedValidAndInvalidProperties(): void
    {
        $processedValues = [
            'name' => ['value' => 'Walmir Silva'],
            'email' => ['value' => 'invalid-email'],
            'age' => ['value' => 25],
        ];

        $errors = [
            'email' => [
                [
                    'errorKey' => 'invalidFormat',
                    'message' => 'Invalid email format',
                ],
            ],
        ];

        $this->attributeHandler
            ->expects($this->once())
            ->method('getProcessedPropertyValues')
            ->willReturn($processedValues);

        $this->attributeHandler
            ->expects($this->once())
            ->method('getProcessingResultErrors')
            ->willReturn($errors);

        $result = $this->processor->process($this->attributeHandler);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertEquals(
            [
                'name' => 'Walmir Silva',
                'email' => 'invalid-email',
                'age' => 25,
            ],
            $result->getValidatedData()
        );
        $this->assertTrue($result->hasErrors());
        $this->assertArrayHasKey('email', $result->getErrors());
        $this->assertEquals('invalidFormat', $result->getErrors()['email'][0]['errorKey']);
        $this->assertEquals('Invalid email format', $result->getErrors()['email'][0]['message']);
    }
}
