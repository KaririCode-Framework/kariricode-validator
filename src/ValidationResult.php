<?php

declare(strict_types=1);

namespace KaririCode\Validator;

use KaririCode\Validator\Contract\ValidationResult as ValidationResultContract;

class ValidationResult implements ValidationResultContract
{
    private array $errors = [];
    private array $validatedData = [];
    private array $errorHashes = [];

    /**
     * Reset all validation state.
     *
     * Clears all errors, validation data, and error hashes,
     * returning the ValidationResult to its initial state.
     */
    public function reset(): void
    {
        $this->errors = [];
        $this->validatedData = [];
        $this->errorHashes = [];
    }

    public function addError(string $property, string $errorKey, string $message): void
    {
        if (!isset($this->errors[$property])) {
            $this->errors[$property] = [];
            $this->errorHashes[$property] = [];
        }

        // Avoid adding duplicate errors
        $hash = md5($errorKey . $message);
        if (isset($this->errorHashes[$property][$hash])) {
            return;
        }

        $this->errorHashes[$property][$hash] = true;
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

    public function __destruct()
    {
        $this->reset();
    }
}
