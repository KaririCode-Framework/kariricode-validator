<?php

declare(strict_types=1);

namespace KaririCode\Validator;

use KaririCode\Validator\Contract\ValidationResult as ValidationResultContract;

class ValidationResult implements ValidationResultContract
{
    private array $errors = [];
    private array $validatedData = [];

    public function addError(string $property, string $errorKey, string $message): void
    {
        if (!isset($this->errors[$property])) {
            $this->errors[$property] = [];
        }

        // Avoid adding duplicate errors
        foreach ($this->errors[$property] as $error) {
            if ($error['errorKey'] === $errorKey) {
                return;
            }
        }

        $this->errors[$property][] = [
            'errorKey' => $errorKey,
            'message' => $message,
        ];
    }

    public function setValidatedData(string $property, mixed $value): void
    {
        $this->validatedData[$property] = $value;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getValidatedData(): array
    {
        return $this->validatedData;
    }

    public function toArray(): array
    {
        return [
            'isValid' => !$this->hasErrors(),
            'errors' => $this->errors,
            'validatedData' => $this->validatedData,
        ];
    }
}
