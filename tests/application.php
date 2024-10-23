<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use KaririCode\ProcessorPipeline\ProcessorRegistry;
use KaririCode\Validator\Attribute\Validate;
use KaririCode\Validator\Contract\ValidationResult;
use KaririCode\Validator\Processor\Date\DateFormatValidator;
use KaririCode\Validator\Processor\Input\EmailValidator;
use KaririCode\Validator\Processor\Input\LengthValidator;
use KaririCode\Validator\Processor\Input\UrlValidator;
use KaririCode\Validator\Processor\Logic\RequiredValidator;
use KaririCode\Validator\Processor\Numeric\IntegerValidator;
use KaririCode\Validator\Processor\Numeric\RangeValidator;
use KaririCode\Validator\Validator;

class UserRegistration
{
    #[Validate(
        processors: [
            'required',
            'length' => ['minLength' => 3, 'maxLength' => 20],
        ],
        messages: [
            'required' => 'Username is required',
            'length' => 'Username must be between 3 and 20 characters',
        ]
    )]
    private string $username = '';

    #[Validate(
        processors: ['required', 'email'],
        messages: [
            'required' => 'Email is required',
            'email' => 'Invalid email format',
        ]
    )]
    private string $email = '';

    #[Validate(
        processors: [
            'required',
            'length' => ['minLength' => 8],
        ],
        messages: [
            'required' => 'Password is required',
            'length' => 'Password must be at least 8 characters long',
        ]
    )]
    private string $password = '';

    #[Validate(
        processors: [
            'required',
            'integer',
            'range' => ['min' => 18, 'max' => 120],
        ],
        messages: [
            'required' => 'Age is required',
            'integer' => 'Age must be a whole number',
            'range' => 'Age must be between 18 and 120',
        ]
    )]
    private int $age = 0;

    #[Validate(
        processors: ['url'],
        messages: [
            'url' => 'Invalid website URL',
        ]
    )]
    private string $website = '';

    #[Validate(
        processors: ['dateFormat' => ['format' => 'Y-m-d']],
        messages: [
            'dateFormat' => 'Invalid date format. Use YYYY-MM-DD',
        ]
    )]
    private string $birthDate = '';

    // Getters and setters
    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    public function getWebsite(): string
    {
        return $this->website;
    }

    public function setWebsite(string $website): void
    {
        $this->website = $website;
    }

    public function getBirthDate(): string
    {
        return $this->birthDate;
    }

    public function setBirthDate(string $birthDate): void
    {
        $this->birthDate = $birthDate;
    }
}

// Set up the validator
$registry = new ProcessorRegistry();
$registry->register('validator', 'required', new RequiredValidator());
$registry->register('validator', 'email', new EmailValidator());
$registry->register('validator', 'length', new LengthValidator());
$registry->register('validator', 'integer', new IntegerValidator());
$registry->register('validator', 'range', new RangeValidator());
$registry->register('validator', 'url', new UrlValidator());
$registry->register('validator', 'dateFormat', new DateFormatValidator());

$validator = new Validator($registry);

// Scenario 1: All validations pass
echo "Scenario 1: All validations pass\n";
$validUser = new UserRegistration();
$validUser->setUsername('walmir_silva');
$validUser->setEmail('walmir.silva@example.com');
$validUser->setPassword('str0ngP@ssw0rd');
$validUser->setAge(35);
$validUser->setWebsite('https://walmirsilva.com');
$validUser->setBirthDate('1988-05-15');

$validationResult1 = $validator->validate($validUser);
displayValidationResult($validationResult1);

// Scenario 2: Multiple validation errors
echo "Scenario 2: Multiple validation errors\n";
$invalidUser = new UserRegistration();
$invalidUser->setUsername('w'); // Too short
$invalidUser->setEmail('invalid-email'); // Invalid email format
$invalidUser->setPassword('weak'); // Too short
$invalidUser->setAge(15); // Below minimum age
$invalidUser->setWebsite('not-a-url'); // Invalid URL
$invalidUser->setBirthDate('15-05-1988'); // Incorrect date format

$validationResult2 = $validator->validate($invalidUser);
displayValidationResult($validationResult2);

// Scenario 3: Some fields valid, some invalid
echo "Scenario 3: Some fields valid, some invalid\n";
$partiallyValidUser = new UserRegistration();
$partiallyValidUser->setUsername('walmir_silva');
$partiallyValidUser->setEmail('walmir.silva@example.com');
$partiallyValidUser->setPassword('short'); // Too short
$partiallyValidUser->setAge(150); // Above maximum age
$partiallyValidUser->setWebsite('https://walmirsilva.com');
$partiallyValidUser->setBirthDate('1988-05-15');

$validationResult3 = $validator->validate($partiallyValidUser);
displayValidationResult($validationResult3);

// Scenario 4: Empty fields (testing required validator)
echo "Scenario 4: Empty fields (testing required validator)\n";
$emptyFieldsUser = new UserRegistration();
// Not setting any fields, leaving them as default empty values

$validationResult4 = $validator->validate($emptyFieldsUser);
displayValidationResult($validationResult4);

// Example of using validated data (for the valid case)
if (!$validationResult1->hasErrors()) {
    $validatedData = $validationResult1->getValidatedData();
    echo "Using validated data:\n";
    echo "Creating user account for: {$validatedData['username']}\n";
    echo "Sending welcome email to: {$validatedData['email']}\n";
    echo "User's age: {$validatedData['age']}\n";
    echo "Website: {$validatedData['website']}\n";
    echo "Birth Date: {$validatedData['birthDate']}\n";
}

// Helper function to display validation results
function displayValidationResult(ValidationResult $result): void
{
    $displayedErrors = [];
    if ($result->hasErrors()) {
        echo "Validation failed. Errors:\n";

        foreach ($result->getErrors() as $property => $errors) {
            foreach ($errors as $error) {
                $errorKey = $property . '-' . $error['errorKey'];
                if (!in_array($errorKey, $displayedErrors, true)) {
                    echo "- $property: {$error['message']} (Error Key: {$error['errorKey']})\n";
                    $displayedErrors[] = $errorKey;
                }
            }
        }
    } else {
        echo "Validation passed successfully.\n";
    }

    echo "\nValidated Data:\n";
    foreach ($result->getValidatedData() as $property => $value) {
        echo "- $property: " . (is_scalar($value) ? $value : gettype($value)) . "\n";
    }
    echo "\n";
}
