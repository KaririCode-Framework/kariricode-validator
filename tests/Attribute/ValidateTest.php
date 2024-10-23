<?php

declare(strict_types=1);

namespace KaririCode\Validator\Tests\Attribute;

use KaririCode\Contract\Processor\Attribute\CustomizableMessageAttribute;
use KaririCode\Contract\Processor\Attribute\ProcessableAttribute;
use KaririCode\Validator\Attribute\Validate;
use PHPUnit\Framework\TestCase;

final class ValidateTest extends TestCase
{
    public function testValidateImplementsProcessableAttribute(): void
    {
        $validate = new Validate([]);
        $this->assertInstanceOf(ProcessableAttribute::class, $validate);
    }

    public function testValidateImplementsCustomizableMessageAttribute(): void
    {
        $validate = new Validate([]);
        $this->assertInstanceOf(CustomizableMessageAttribute::class, $validate);
    }

    public function testValidateIsAttribute(): void
    {
        $reflectionClass = new \ReflectionClass(Validate::class);
        $attributes = $reflectionClass->getAttributes();

        $this->assertCount(1, $attributes);
        $this->assertSame(\Attribute::class, $attributes[0]->getName());
        $this->assertSame([\Attribute::TARGET_PROPERTY], $attributes[0]->getArguments());
    }

    /**
     * @dataProvider validProcessorsProvider
     */
    public function testConstructorWithValidProcessors(array $processors, array $expected): void
    {
        $validate = new Validate($processors);
        $this->assertEquals($expected, $validate->getProcessors());
    }

    public function testConstructorFiltersInvalidProcessors(): void
    {
        $processors = ['required', null, false, 'email'];
        $expectedProcessors = ['required', 'email'];
        $validate = new Validate($processors);

        $this->assertEquals($expectedProcessors, $validate->getProcessors());
    }

    public function testConstructorWithEmptyProcessors(): void
    {
        $validate = new Validate([]);
        $this->assertEmpty($validate->getProcessors());
    }

    /**
     * @dataProvider messageProvider
     */
    public function testGetMessage(array $processors, array $messages, string $processor, ?string $expected): void
    {
        $validate = new Validate($processors, $messages);
        $this->assertSame($expected, $validate->getMessage($processor));
    }

    public function testGetProcessorsReturnsProcessors(): void
    {
        $processors = ['required', 'email'];
        $validate = new Validate($processors);

        $this->assertEquals($processors, $validate->getProcessors());
    }

    public static function validProcessorsProvider(): array
    {
        return [
            'single processor' => [
                ['required'],
                ['required'],
            ],
            'multiple processors' => [
                ['required', 'email', 'length'],
                ['required', 'email', 'length'],
            ],
            'processor with config' => [
                [
                    'length' => ['minLength' => 3, 'maxLength' => 20],
                ],
                ['length'],
            ],
            'mixed processors' => [
                [
                    'required',
                    'email' => ['message' => 'Invalid email'],
                ],
                ['required', 'email'],
            ],
        ];
    }

    public static function messageProvider(): array
    {
        return [
            'existing message' => [
                ['required'],
                ['required' => 'Field is required'],
                'required',
                'Field is required',
            ],
            'non-existing message' => [
                ['required'],
                ['required' => 'Field is required'],
                'email',
                null,
            ],
            'empty messages' => [
                ['required'],
                [],
                'required',
                null,
            ],
            'message for non-registered processor' => [
                ['required'],
                ['email' => 'Invalid email'],
                'required',
                null,
            ],
            'null message' => [
                ['required'],
                ['required' => null],
                'required',
                null,
            ],
        ];
    }
}
