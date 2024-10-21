<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor;

use KaririCode\PropertyInspector\AttributeHandler;
use KaririCode\Validator\Contract\ValidationResult as ValidationResultContract;
use KaririCode\Validator\Contract\ValidationResultProcessor;
use KaririCode\Validator\ValidationResult;

class DefaultValidationResultProcessor implements ValidationResultProcessor
{
    public function __construct(
        private ValidationResultContract $result = new ValidationResult()
    ) {
    }

    public function process(AttributeHandler $handler): ValidationResult
    {
        $processedValues = $handler->getProcessedPropertyValues();
        $errors = $handler->getProcessingResultErrors();

        foreach ($processedValues as $property => $data) {
            $this->result->setValidatedData($property, $data['value']);

            if (isset($errors[$property])) {
                $this->addPropertyErrors($this->result, $property, $errors[$property]);
            }
        }

        return $this->result;
    }

    private function addPropertyErrors(
        ValidationResult $result,
        string $property,
        array $propertyErrors
    ): void {
        foreach ($propertyErrors as $error) {
            $result->addError($property, $error['errorKey'], $error['message']);
        }
    }
}
