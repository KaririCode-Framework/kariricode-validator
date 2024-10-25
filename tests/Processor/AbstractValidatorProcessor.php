<?php

declare(strict_types=1);

namespace KaririCode\Validator\Tests\Processor;

use KaririCode\Validator\Processor\AbstractValidatorProcessor;
use PHPUnit\Framework\TestCase;

final class TestValidatorProcessor extends AbstractValidatorProcessor
{
    private int $minimumValue;

    public function __construct(int $minimumValue = 0)
    {
        $this->minimumValue = $minimumValue;
    }

    public function process(mixed $input): mixed
    {
        if (!is_int($input)) {
            $this->setInvalid('invalidType');

            return $input;
        }

        if ($input < $this->minimumValue) {
            $this->setInvalid('belowMinimum');

            return $input;
        }

        return $input;
    }

    public function getIsValidState(): bool
    {
        return $this->isValid;
    }

    public function getErrorKeyState(): string
    {
        return $this->errorKey;
    }
}

final class AbstractValidatorProcessorTest extends TestCase
{
    private TestValidatorProcessor $processor;

    protected function setUp(): void
    {
        $this->processor = new TestValidatorProcessor(5);
    }

    public function testRestoreInitialStateAfterReset(): void
    {
        $this->assertTrue($this->processor->getIsValidState());
        $this->assertEmpty($this->processor->getErrorKeyState());

        $this->processor->process('invalid');

        $this->assertFalse($this->processor->getIsValidState());
        $this->assertNotEmpty($this->processor->getErrorKeyState());

        $this->processor->reset();

        $this->assertTrue($this->processor->getIsValidState());
        $this->assertEmpty($this->processor->getErrorKeyState());
    }

    public function testMultipleInvalidationsWithReset(): void
    {
        $this->processor->process('invalid');
        $this->assertFalse($this->processor->getIsValidState());
        $this->assertSame('invalidType', $this->processor->getErrorKeyState());

        $this->processor->reset();
        $this->assertTrue($this->processor->getIsValidState());
        $this->assertEmpty($this->processor->getErrorKeyState());

        $this->processor->process(3);
        $this->assertFalse($this->processor->getIsValidState());
        $this->assertSame('belowMinimum', $this->processor->getErrorKeyState());

        $this->processor->reset();
        $this->assertTrue($this->processor->getIsValidState());
        $this->assertEmpty($this->processor->getErrorKeyState());
    }

    public function testProcessingBehaviorAfterReset(): void
    {
        $this->processor->process('invalid');
        $this->processor->reset();

        $validInput = 10;
        $result = $this->processor->process($validInput);

        $this->assertSame($validInput, $result);
        $this->assertTrue($this->processor->getIsValidState());
        $this->assertEmpty($this->processor->getErrorKeyState());
    }

    public function testResetAfterValidInput(): void
    {
        $this->processor->process(10);

        $this->assertTrue($this->processor->getIsValidState());
        $this->assertEmpty($this->processor->getErrorKeyState());

        $this->processor->reset();

        $this->assertTrue($this->processor->getIsValidState());
        $this->assertEmpty($this->processor->getErrorKeyState());
    }

    public function testSequentialProcessingWithReset(): void
    {
        $sequence = [
            ['input' => 'invalid', 'expectedValid' => false, 'expectedError' => 'invalidType'],
            ['input' => 10, 'expectedValid' => true, 'expectedError' => ''],
            ['input' => 3, 'expectedValid' => false, 'expectedError' => 'belowMinimum'],
        ];

        foreach ($sequence as $step) {
            $this->processor->reset();
            $this->processor->process($step['input']);

            $this->assertSame($step['expectedValid'], $this->processor->getIsValidState());
            $this->assertSame($step['expectedError'], $this->processor->getErrorKeyState());
        }
    }

    public function testConsecutiveResetsValidState(): void
    {
        $this->processor->process('invalid');

        for ($i = 0; $i < 3; ++$i) {
            $this->processor->reset();
            $this->assertTrue($this->processor->getIsValidState());
            $this->assertEmpty($this->processor->getErrorKeyState());
        }
    }
}
