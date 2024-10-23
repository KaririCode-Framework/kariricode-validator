<?php

declare(strict_types=1);

namespace KaririCode\Validator\Tests\Processor\Input;

use KaririCode\Validator\Processor\Input\UrlValidator;
use PHPUnit\Framework\TestCase;

final class UrlValidatorTest extends TestCase
{
    private UrlValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new UrlValidator();
    }

    /**
     * @dataProvider validUrlProvider
     */
    public function testValidUrls(string $url): void
    {
        $this->validator->process($url);

        $this->assertTrue($this->validator->isValid());
        $this->assertEmpty($this->validator->getErrorKey());
    }

    /**
     * @dataProvider invalidUrlProvider
     */
    public function testInvalidUrls(string $url): void
    {
        $this->validator->process($url);

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('invalidFormat', $this->validator->getErrorKey());
    }

    public function testInvalidType(): void
    {
        $this->validator->process(123);

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('invalidType', $this->validator->getErrorKey());
    }

    public static function validUrlProvider(): array
    {
        return [
            ['https://example.com'],
            ['http://subdomain.example.com'],
            ['https://example.com/path'],
            ['http://example.com:8080'],
            ['https://example.com/path?param=value'],
        ];
    }

    public static function invalidUrlProvider(): array
    {
        return [
            ['example.com'],
            ['not a url'],
            ['http://'],
            ['https://'],
            ['ftp:/example.com'],
        ];
    }
}
