<?php

declare(strict_types=1);

namespace KaririCode\Validator\Result;

use KaririCode\ProcessorPipeline\Result\ProcessingResultCollection;
use KaririCode\Validator\Contract\ValidationResult as ValidationResultContract;

final class ValidationResult implements ValidationResultContract
{
    public function __construct(
        private readonly ProcessingResultCollection $results
    ) {
    }

    public function isValid(): bool
    {
        return !$this->results->hasErrors();
    }

    public function getErrors(): array
    {
        return $this->results->getErrors();
    }

    public function getValidatedData(): array
    {
        return $this->results->getProcessedData();
    }

    public function toArray(): array
    {
        return $this->results->toArray();
    }
}
