# KaririCode Framework: Validator Component

A powerful and flexible data validation component for PHP, part of the KaririCode Framework. It uses attribute-based validation with configurable processors to ensure data integrity and validation in your applications.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
  - [Basic Usage](#basic-usage)
  - [Advanced Usage: User Registration](#advanced-usage-user-registration)
- [Available Validators](#available-validators)
  - [Input Validators](#input-validators)
  - [Numeric Validators](#numeric-validators)
  - [Logic Validators](#logic-validators)
  - [Date Validators](#date-validators)
- [Configuration](#configuration)
- [Integration with Other KaririCode Components](#integration-with-other-kariricode-components)
- [Development and Testing](#development-and-testing)
- [Contributing](#contributing)
- [License](#license)
- [Support and Community](#support-and-community)

## Features

- Attribute-based validation for object properties
- Comprehensive set of built-in validators for common use cases
- Easy integration with other KaririCode components
- Configurable processors for customized validation logic
- Support for custom error messages
- Extensible architecture allowing custom validators
- Robust error handling and reporting
- Chainable validation pipelines for complex data validation
- Built-in support for multiple validation scenarios
- Type-safe validation with PHP 8.3 features

## Installation

You can install the Validator component via Composer:

```bash
composer require kariricode/validator
```

### Requirements

- PHP 8.3 or higher
- Composer
- Extensions: `ext-mbstring`, `ext-filter`

## Usage

### Basic Usage

1. Define your data class with validation attributes:

```php
use KaririCode\Validator\Attribute\Validate;

class UserProfile
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

    // Getters and setters...
}
```

2. Set up the validator and use it:

```php
use KaririCode\ProcessorPipeline\ProcessorRegistry;
use KaririCode\Validator\Validator;
use KaririCode\Validator\Processor\Logic\RequiredValidator;
use KaririCode\Validator\Processor\Input\LengthValidator;
use KaririCode\Validator\Processor\Input\EmailValidator;

$registry = new ProcessorRegistry();
$registry->register('validator', 'required', new RequiredValidator());
$registry->register('validator', 'length', new LengthValidator());
$registry->register('validator', 'email', new EmailValidator());

$validator = new Validator($registry);

$userProfile = new UserProfile();
$userProfile->setUsername('wa');  // Too short
$userProfile->setEmail('invalid-email');  // Invalid format

$result = $validator->validate($userProfile);

if ($result->hasErrors()) {
    foreach ($result->getErrors() as $property => $errors) {
        foreach ($errors as $error) {
            echo "$property: {$error['message']}\n";
        }
    }
}
```

### Advanced Usage: User Registration

Here's an example of how to use the KaririCode Validator in a real-world scenario, such as validating user registration data:

```php
use KaririCode\Validator\Attribute\Validate;

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

    // Getters and setters...
}

// Usage example
$registration = new UserRegistration();
$registration->setUsername('wm');  // Too short
$registration->setEmail('invalid');  // Invalid format
$registration->setPassword('weak');  // Too short
$registration->setAge(15);  // Too young

$result = $validator->validate($registration);

// Process validation results
if ($result->hasErrors()) {
    $errors = $result->getErrors();
    // Handle validation errors
} else {
    $validatedData = $result->getValidatedData();
    // Process valid registration
}
```

## Available Validators

### Input Validators

- **EmailValidator**: Validates email addresses using PHP's filter_var function.

  - Error Keys:
    - `invalidType`: Input is not a string
    - `invalidFormat`: Invalid email format

- **LengthValidator**: Validates string length within specified bounds.

  - **Configuration Options**:
    - `minLength`: Minimum allowed length
    - `maxLength`: Maximum allowed length
  - Error Keys:
    - `invalidType`: Input is not a string
    - `tooShort`: String is shorter than minLength
    - `tooLong`: String is longer than maxLength

- **UrlValidator**: Validates URLs using PHP's filter_var function.
  - Error Keys:
    - `invalidType`: Input is not a string
    - `invalidFormat`: Invalid URL format

### Numeric Validators

- **IntegerValidator**: Ensures the input is a valid integer.

  - Error Keys:
    - `notAnInteger`: Input is not a valid integer

- **RangeValidator**: Validates numeric values within a specified range.
  - **Configuration Options**:
    - `min`: Minimum allowed value
    - `max`: Maximum allowed value
  - Error Keys:
    - `notNumeric`: Input is not a number
    - `outOfRange`: Value is outside specified range

### Logic Validators

- **RequiredValidator**: Ensures a value is not empty.
  - Error Keys:
    - `missingValue`: Required value is missing or empty

### Date Validators

- **DateFormatValidator**: Validates dates against a specified format.

  - **Configuration Options**:
    - `format`: Date format string (default: 'Y-m-d')
  - Error Keys:
    - `invalidType`: Input is not a string
    - `invalidFormat`: Date doesn't match specified format

- **DateRangeValidator**: Validates dates within a specified range.
  - **Configuration Options**:
    - `minDate`: Minimum allowed date
    - `maxDate`: Maximum allowed date
    - `format`: Date format string (default: 'Y-m-d')
  - Error Keys:
    - `invalidType`: Input is not a string
    - `invalidDate`: Invalid date format
    - `outOfRange`: Date is outside specified range

## Configuration

The Validator component can be configured globally or per-validator basis. Here's an example of how to configure the `LengthValidator`:

```php
use KaririCode\Validator\Processor\Input\LengthValidator;

$lengthValidator = new LengthValidator();
$lengthValidator->configure([
    'minLength' => 3,
    'maxLength' => 20,
]);

$registry->register('validator', 'length', $lengthValidator);
```

## Integration with Other KaririCode Components

The Validator component is designed to work seamlessly with other KaririCode components:

- **KaririCode\Contract**: Provides interfaces and contracts for consistent component integration.
- **KaririCode\ProcessorPipeline**: Utilized for building and executing validation pipelines.
- **KaririCode\PropertyInspector**: Used for analyzing and processing object properties with validation attributes.

## Registry Explanation

The registry is a central component for managing validators. Here's how to set up a complete registry:

```php
// Create and configure the registry
$registry = new ProcessorRegistry();

// Register all required validators
$registry->register('validator', 'required', new RequiredValidator());
$registry->register('validator', 'email', new EmailValidator());
$registry->register('validator', 'length', new LengthValidator());
$registry->register('validator', 'integer', new IntegerValidator());
$registry->register('validator', 'range', new RangeValidator());
$registry->register('validator', 'url', new UrlValidator());
$registry->register('validator', 'dateFormat', new DateFormatValidator());
$registry->register('validator', 'dateRange', new DateRangeValidator());
```

## Development and Testing

For development and testing purposes, this package uses Docker and Docker Compose to ensure consistency across different environments. A Makefile is provided for convenience.

### Prerequisites

- Docker
- Docker Compose
- Make (optional, but recommended for easier command execution)

### Development Setup

1. Clone the repository:

   ```bash
   git clone https://github.com/KaririCode-Framework/kariricode-validator.git
   cd kariricode-validator
   ```

2. Set up the environment:

   ```bash
   make setup-env
   ```

3. Start the Docker containers:

   ```bash
   make up
   ```

4. Install dependencies:

   ```bash
   make composer-install
   ```

### Available Make Commands

- `make up`: Start all services in the background
- `make down`: Stop and remove all containers
- `make build`: Build Docker images
- `make shell`: Access the PHP container shell
- `make test`: Run tests
- `make coverage`: Run test coverage with visual formatting
- `make cs-fix`: Run PHP CS Fixer to fix code style
- `make quality`: Run all quality commands (cs-check, test, security-check)

## Contributing

We welcome contributions to the KaririCode Validator component! Here's how you can contribute:

1. Fork the repository
2. Create a new branch for your feature or bug fix
3. Write tests for your changes
4. Implement your changes
5. Run the test suite and ensure all tests pass
6. Submit a pull request with a clear description of your changes

Please read our [Contributing Guide](CONTRIBUTING.md) for more details on our code of conduct and development process.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support and Community

- **Documentation**: [https://kariricode.org/docs/validator](https://kariricode.org/docs/validator)
- **Issue Tracker**: [GitHub Issues](https://github.com/KaririCode-Framework/kariricode-validator/issues)
- **Community Forum**: [KaririCode Club Community](https://kariricode.club)
- **Stack Overflow**: Tag your questions with `kariricode-validator`

---

Built with ❤️ by the KaririCode team. Empowering developers to create more secure and robust PHP applications.
