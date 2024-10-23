<?php

declare(strict_types=1);

namespace KaririCode\Validator\Tests\Processor\Date;

use KaririCode\Validator\Exception\MissingProcessorConfigException;
use KaririCode\Validator\Processor\Date\DateRangeValidator;
use PHPUnit\Framework\TestCase;

final class DateRangeValidatorTest extends TestCase
{
    private DateRangeValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new DateRangeValidator();
    }

    public function testValidDateInRange(): void
    {
        $this->validator->configure([
            'minDate' => '2024-01-01',
            'maxDate' => '2024-12-31',
        ]);

        $this->validator->process('2024-06-15');

        $this->assertTrue($this->validator->isValid());
        $this->assertEmpty($this->validator->getErrorKey());
    }

    public function testDateBeforeRange(): void
    {
        $this->validator->configure([
            'minDate' => '2024-01-01',
            'maxDate' => '2024-12-31',
        ]);

        $this->validator->process('2023-12-31');

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('outOfRange', $this->validator->getErrorKey());
    }

    public function testDateAfterRange(): void
    {
        $this->validator->configure([
            'minDate' => '2024-01-01',
            'maxDate' => '2024-12-31',
        ]);

        $this->validator->process('2025-01-01');

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('outOfRange', $this->validator->getErrorKey());
    }

    public function testInvalidDateFormat(): void
    {
        $this->validator->configure([
            'minDate' => '2024-01-01',
            'maxDate' => '2024-12-31',
        ]);

        $this->validator->process('2024/06/15');

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('invalidDate', $this->validator->getErrorKey());
    }

    public function testInvalidType(): void
    {
        $this->validator->configure([
            'minDate' => '2024-01-01',
            'maxDate' => '2024-12-31',
        ]);

        $this->validator->process(123);

        $this->assertFalse($this->validator->isValid());
        $this->assertSame('invalidType', $this->validator->getErrorKey());
    }

    public function testMissingMinDateThrowsException(): void
    {
        $this->expectException(MissingProcessorConfigException::class);
        $this->validator->configure([
            'maxDate' => '2024-12-31',
        ]);
    }

    public function testMissingMaxDateThrowsException(): void
    {
        $this->expectException(MissingProcessorConfigException::class);
        $this->validator->configure([
            'minDate' => '2024-01-01',
        ]);
    }

    public function testInvalidMinMaxOrderThrowsException(): void
    {
        $this->expectException(MissingProcessorConfigException::class);
        $this->validator->configure([
            'minDate' => '2024-12-31',
            'maxDate' => '2024-01-01',
        ]);
    }

    public function testCustomDateFormat(): void
    {
        $this->validator->configure([
            'minDate' => '01/01/2024',
            'maxDate' => '31/12/2024',
            'format' => 'd/m/Y',
        ]);

        $this->validator->process('15/06/2024');

        $this->assertTrue($this->validator->isValid());
        $this->assertEmpty($this->validator->getErrorKey());
    }
}
