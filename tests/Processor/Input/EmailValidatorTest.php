<?php

declare(strict_types=1);

namespace KaririCode\Validator\Tests\Processor\Input;

use KaririCode\Validator\Processor\Input\EmailValidator;
use PHPUnit\Framework\TestCase;

final class EmailValidatorTest extends TestCase
{
    private EmailValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new EmailValidator();
    }

    /**
     * @dataProvider validEmailProvider
     */
    public function testValidEmails(string $email): void
    {
        $this->validator->process($email);

        $this->assertTrue($this->validator->isValid());
        $this->assertEmpty($this->validator->getErrorKey());
    }

    /**
     * @dataProvider invalidEmailProvider
     */
    public function testInvalidEmails(string $email): void
    {
        $this->validator->process($email);

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('invalidFormat', $this->validator->getErrorKey());
    }

    public function testInvalidType(): void
    {
        $this->validator->process(123);

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('invalidType', $this->validator->getErrorKey());
    }

    public static function validEmailProvider(): array
    {
        return [
            ['test@example.com'],
            ['user.name@domain.com'],
            ['user+tag@domain.com'],
            ['user@subdomain.domain.com'],
        ];
    }

    public static function invalidEmailProvider(): array
    {
        return [
            ['test@'],
            ['@domain.com'],
            ['test@domain'],
            ['test.domain.com'],
            ['test@domain..com'],
        ];
    }
}
