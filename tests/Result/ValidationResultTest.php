<?php

declare(strict_types=1);

namespace KaririCode\Validator\Tests\Result;

use KaririCode\ProcessorPipeline\Result\ProcessingResultCollection;
use KaririCode\Validator\Result\ValidationResult;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ValidationResultTest extends TestCase
{
    private ProcessingResultCollection|MockObject $results;
    private ValidationResult $validationResult;

    protected function setUp(): void
    {
        $this->results = $this->createMock(ProcessingResultCollection::class);
        $this->validationResult = new ValidationResult($this->results);
    }

    public function testInitialState(): void
    {
        $this->results->expects($this->once())
            ->method('hasErrors')
            ->willReturn(false);

        $this->results->expects($this->once())
            ->method('getErrors')
            ->willReturn([]);

        $this->results->expects($this->once())
            ->method('getProcessedData')
            ->willReturn([]);

        $this->assertTrue($this->validationResult->isValid());
        $this->assertEmpty($this->validationResult->getErrors());
        $this->assertEmpty($this->validationResult->getValidatedData());
    }

    public function testWithValidatedData(): void
    {
        $validatedData = ['name' => 'John', 'email' => 'john@example.com'];

        $this->results->expects($this->any())
            ->method('getProcessedData')
            ->willReturn($validatedData);

        $this->assertSame($validatedData, $this->validationResult->getValidatedData());
    }

    public function testToArrayWithErrorsAndData(): void
    {
        $data = [
            'name' => 'John',
            'email' => 'john@example.com',
        ];

        $errors = [
            'email' => [
                'format' => 'Invalid email format',
            ],
        ];

        $expected = [
            'isValid' => false,
            'errors' => $errors,
            'data' => $data,
        ];

        $this->results->expects($this->any())
            ->method('toArray')
            ->willReturn($expected);

        $this->assertSame($expected, $this->validationResult->toArray());
    }

    public function testIsValid(): void
    {
        $this->results->expects($this->once())
            ->method('hasErrors')
            ->willReturn(false);

        $this->assertTrue($this->validationResult->isValid());
    }

    public function testIsInvalid(): void
    {
        $this->results->expects($this->once())
            ->method('hasErrors')
            ->willReturn(true);

        $this->assertFalse($this->validationResult->isValid());
    }
}
