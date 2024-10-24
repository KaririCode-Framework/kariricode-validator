<?php

declare(strict_types=1);

namespace KaririCode\Validator\Processor;

use KaririCode\PropertyInspector\AttributeHandler;
use KaririCode\Validator\Contract\ValidationResultProcessor;
use KaririCode\Validator\ValidationResult;

class DefaultValidationResultProcessor implements ValidationResultProcessor
{
    public function process(AttributeHandler $handler): ValidationResult
    {
        $result = new ValidationResult();
        $processedValues = $handler->getProcessedPropertyValues();
        $errors = $handler->getProcessingResultErrors();

        foreach ($processedValues as $property => $data) {
            $result->setValidatedData($property, $data['value']);

            if (isset($errors[$property])) {
                $this->addPropertyErrors($result, $property, $errors[$property]);
            }
        }

        return $result;
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
