<?php

declare(strict_types=1);

namespace KaririCode\Validator\Tests\Processor\Input;

use KaririCode\Validator\Exception\MissingProcessorConfigException;
use KaririCode\Validator\Processor\Input\LengthValidator;
use PHPUnit\Framework\TestCase;

final class LengthValidatorTest extends TestCase
{
    private LengthValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new LengthValidator();
    }

    public function testValidLength(): void
    {
        $this->validator->configure(['minLength' => 3, 'maxLength' => 10]);
        $input = 'test';

        $this->validator->process($input);

        $this->assertTrue($this->validator->isValid());
        $this->assertEmpty($this->validator->getErrorKey());
    }

    public function testTooShort(): void
    {
        $this->validator->configure(['minLength' => 5, 'maxLength' => 10]);
        $input = 'test';

        $this->validator->process($input);

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('tooShort', $this->validator->getErrorKey());
    }

    public function testTooLong(): void
    {
        $this->validator->configure(['minLength' => 2, 'maxLength' => 5]);
        $input = 'testing';

        $this->validator->process($input);

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('tooLong', $this->validator->getErrorKey());
    }

    public function testInvalidType(): void
    {
        $this->validator->configure(['minLength' => 2, 'maxLength' => 5]);

        $this->validator->process(123);

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('invalidType', $this->validator->getErrorKey());
    }

    public function testEmptyConfigThrowsException(): void
    {
        $this->expectException(MissingProcessorConfigException::class);
        $this->validator->configure([]);
    }

    public function testMissingMinLengthThrowsException(): void
    {
        $this->expectException(MissingProcessorConfigException::class);
        $this->validator->configure(['maxLength' => 5]);
    }

    public function testMissingMaxLengthThrowsException(): void
    {
        $this->expectException(MissingProcessorConfigException::class);
        $this->validator->configure(['minLength' => 2]);
    }
}
