<?php

declare(strict_types=1);

namespace KaririCode\Validator;

use KaririCode\Contract\Processor\ProcessorRegistry;
use KaririCode\Contract\Validator\Validator as ValidatorContract;
use KaririCode\ProcessorPipeline\Handler\ProcessorAttributeHandler;
use KaririCode\ProcessorPipeline\ProcessorBuilder;
use KaririCode\PropertyInspector\AttributeAnalyzer;
use KaririCode\PropertyInspector\Utility\PropertyInspector;
use KaririCode\Validator\Attribute\Validate;
use KaririCode\Validator\Result\ValidationResult;

final class Validator implements ValidatorContract
{
    private const IDENTIFIER = 'validator';

    private readonly ProcessorBuilder $builder;

    public function __construct(
        private readonly ProcessorRegistry $registry
    ) {
        $this->builder = new ProcessorBuilder($this->registry);
    }

    public function validate(mixed $object): ValidationResult
    {
        $handler = new ProcessorAttributeHandler(
            self::IDENTIFIER,
            $this->builder
        );

        $propertyInspector = new PropertyInspector(
            new AttributeAnalyzer(Validate::class)
        );

        /** @var PropertyAttributeHandler */
        $handler = $propertyInspector->inspect($object, $handler);

        return new ValidationResult(
            $handler->getProcessingResults()
        );
    }
}
