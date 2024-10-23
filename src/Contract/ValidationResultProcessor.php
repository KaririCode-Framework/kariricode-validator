<?php

declare(strict_types=1);

namespace KaririCode\Validator\Contract;

use KaririCode\PropertyInspector\AttributeHandler;

interface ValidationResultProcessor
{
    public function process(AttributeHandler $handler): ValidationResult;
}
