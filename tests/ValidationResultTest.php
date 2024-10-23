<?php

declare(strict_types=1);

namespace KaririCode\Tests\Validator;

use KaririCode\Validator\ValidationResult;
use PHPUnit\Framework\TestCase;

class ValidationResultTest extends TestCase
{
    private ValidationResult $validationResult;

    protected function setUp(): void
    {
        $this->validationResult = new ValidationResult();
    }

    public function testInitialState(): void
    {
        $this->assertFalse($this->validationResult->hasErrors());
        $this->assertEmpty($this->validationResult->getErrors());
        $this->assertEmpty($this->validationResult->getValidatedData());

        $expectedArray = [
            'isValid' => true,
            'errors' => [],
            'validatedData' => [],
        ];
        $this->assertEquals($expectedArray, $this->validationResult->toArray());
    }

    public function testAddError(): void
    {
        $this->validationResult->addError('email', 'invalidFormat', 'Invalid email format');

        $this->assertTrue($this->validationResult->hasErrors());

        $expectedErrors = [
            'email' => [
                [
                    'errorKey' => 'invalidFormat',
                    'message' => 'Invalid email format',
                ],
            ],
        ];
        $this->assertEquals($expectedErrors, $this->validationResult->getErrors());
    }

    public function testAddMultipleErrorsForSameProperty(): void
    {
        $this->validationResult->addError('password', 'tooShort', 'Password is too short');
        $this->validationResult->addError('password', 'complexity', 'Password needs special characters');

        $this->assertTrue($this->validationResult->hasErrors());

        $expectedErrors = [
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
        $this->assertEquals($expectedErrors, $this->validationResult->getErrors());
    }

    public function testAddDuplicateError(): void
    {
        $this->validationResult->addError('email', 'invalidFormat', 'Invalid email format');
        $this->validationResult->addError('email', 'invalidFormat', 'Invalid email format');

        $expectedErrors = [
            'email' => [
                [
                    'errorKey' => 'invalidFormat',
                    'message' => 'Invalid email format',
                ],
            ],
        ];
        $this->assertEquals($expectedErrors, $this->validationResult->getErrors());
        $this->assertCount(1, $this->validationResult->getErrors()['email']);
    }

    public function testSetValidatedData(): void
    {
        $this->validationResult->setValidatedData('name', 'Walmir Silva');
        $this->validationResult->setValidatedData('age', 30);

        $expectedData = [
            'name' => 'Walmir Silva',
            'age' => 30,
        ];
        $this->assertEquals($expectedData, $this->validationResult->getValidatedData());
    }

    public function testOverwriteValidatedData(): void
    {
        $this->validationResult->setValidatedData('age', 30);
        $this->validationResult->setValidatedData('age', 31);

        $expectedData = ['age' => 31];
        $this->assertEquals($expectedData, $this->validationResult->getValidatedData());
    }

    public function testToArrayWithValidData(): void
    {
        $this->validationResult->setValidatedData('name', 'Walmir Silva');
        $this->validationResult->setValidatedData('email', 'walmir@example.com');

        $expected = [
            'isValid' => true,
            'errors' => [],
            'validatedData' => [
                'name' => 'Walmir Silva',
                'email' => 'walmir@example.com',
            ],
        ];

        $this->assertEquals($expected, $this->validationResult->toArray());
    }

    public function testToArrayWithErrors(): void
    {
        $this->validationResult->setValidatedData('email', 'invalid');
        $this->validationResult->addError('email', 'invalidFormat', 'Invalid email format');

        $expected = [
            'isValid' => false,
            'errors' => [
                'email' => [
                    [
                        'errorKey' => 'invalidFormat',
                        'message' => 'Invalid email format',
                    ],
                ],
            ],
            'validatedData' => [
                'email' => 'invalid',
            ],
        ];

        $this->assertEquals($expected, $this->validationResult->toArray());
    }

    public function testSetValidatedDataWithDifferentTypes(): void
    {
        $testData = [
            'string' => 'test string',
            'integer' => 42,
            'float' => 3.14,
            'boolean' => true,
            'array' => ['a', 'b', 'c'],
            'null' => null,
            'object' => new \stdClass(),
        ];

        foreach ($testData as $key => $value) {
            $this->validationResult->setValidatedData($key, $value);
        }

        $validatedData = $this->validationResult->getValidatedData();
        foreach ($testData as $key => $value) {
            $this->assertSame($value, $validatedData[$key]);
        }
    }

    public function testErrorsForMultipleProperties(): void
    {
        $this->validationResult->addError('username', 'required', 'Username is required');
        $this->validationResult->addError('email', 'invalidFormat', 'Invalid email format');
        $this->validationResult->addError('password', 'tooShort', 'Password is too short');

        $this->assertTrue($this->validationResult->hasErrors());
        $errors = $this->validationResult->getErrors();

        $this->assertCount(3, $errors);
        $this->assertArrayHasKey('username', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('password', $errors);
    }

    public function testMixedValidAndInvalidData(): void
    {
        $this->validationResult->setValidatedData('name', 'Walmir Silva');
        $this->validationResult->setValidatedData('age', 25);

        $this->validationResult->addError('email', 'required', 'Email is required');
        $this->validationResult->addError('password', 'tooShort', 'Password is too short');

        $result = $this->validationResult->toArray();

        $this->assertFalse($result['isValid']);
        $this->assertCount(2, $result['errors']);
        $this->assertCount(2, $result['validatedData']);

        $this->assertEquals('Walmir Silva', $result['validatedData']['name']);
        $this->assertEquals(25, $result['validatedData']['age']);

        $this->assertEquals('Email is required', $result['errors']['email'][0]['message']);
        $this->assertEquals('Password is too short', $result['errors']['password'][0]['message']);
    }
}
