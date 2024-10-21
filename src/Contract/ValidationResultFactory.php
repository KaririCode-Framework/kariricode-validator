<?php

declare(strict_types=1);

namespace KaririCode\Validator\Contract;

interface ValidationResultFactory
{
    public function create(): ValidationResult;
}
