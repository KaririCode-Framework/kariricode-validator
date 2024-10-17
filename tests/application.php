<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use KaririCode\ProcessorPipeline\ProcessorRegistry;
use KaririCode\Validator\Attribute\Validate;
use KaririCode\Validator\Processor\Input\EmailValidator;
use KaririCode\Validator\Processor\Input\UrlValidator;
use KaririCode\Validator\Processor\Input\LengthValidator;
use KaririCode\Validator\Processor\Numeric\RangeValidator;
use KaririCode\Validator\Processor\Numeric\IntegerValidator;
use KaririCode\Validator\Processor\Date\DateFormatValidator;
use KaririCode\Validator\Processor\Date\DateRangeValidator;
use KaririCode\Validator\Processor\Logic\RequiredValidator;
use KaririCode\Validator\Processor\Logic\ConditionalValidator;
use KaririCode\Validator\Validator;

class UserProfile
{
    #[Validate(validators: ['required', 'length' => ['minLength' => 2, 'maxLength' => 50]])]
    private string $name = '';

    #[Validate(validators: ['required', 'email'])]
    private string $email = '';

    #[Validate(validators: ['required', 'integer', 'range' => ['min' => 0, 'max' => 120]])]
    private int $age = 0;

    #[Validate(validators: ['url'])]
    private string $website = '';

    #[Validate(validators: ['conditional_phone'])]
    private string $phoneNumber = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
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

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }
}

class Event
{
    #[Validate(validators: ['required', 'date_format' => ['format' => 'Y-m-d']])]
    private string $eventDate = '';

    #[Validate(validators: ['date_range' => ['minDate' => '2023-01-01', 'maxDate' => '2023-12-31', 'format' => 'Y-m-d']])]
    private string $registrationDeadline = '';

    public function getEventDate(): string
    {
        return $this->eventDate;
    }

    public function setEventDate(string $eventDate): void
    {
        $this->eventDate = $eventDate;
    }

    public function getRegistrationDeadline(): string
    {
        return $this->registrationDeadline;
    }

    public function setRegistrationDeadline(string $registrationDeadline): void
    {
        $this->registrationDeadline = $registrationDeadline;
    }
}

$registry = new ProcessorRegistry();
$registry->register('validator', 'required', new RequiredValidator());
$registry->register('validator', 'date_format', new DateFormatValidator());

$validator = new Validator($registry);

$event = new Event();
$event->setEventDate('2023-13-01');
$event->setRegistrationDeadline('2024-01-01');


$validationResults = $validator->validate($event);

// var_dump($validationResults);
// var_dump($event);
