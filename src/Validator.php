<?php

declare(strict_types=1);

namespace KaririCode\Validator;

use KaririCode\Contract\Processor\ProcessorRegistry;
use KaririCode\Contract\Validator\Validator as ValidatorContract;
use KaririCode\ProcessorPipeline\ProcessorBuilder;
use KaririCode\PropertyInspector\AttributeAnalyzer;
use KaririCode\PropertyInspector\AttributeHandler;
use KaririCode\PropertyInspector\Utility\PropertyInspector;
use KaririCode\Validator\Attribute\Validate;
use KaririCode\Validator\Processor\DefaultValidationResultProcessor;

class Validator implements ValidatorContract
{
    private const IDENTIFIER = 'validator';

    private ProcessorBuilder $builder;

    public function __construct(
        private readonly ProcessorRegistry $registry,
    ) {
        $this->builder = new ProcessorBuilder($this->registry);
    }

    public function validate(mixed $object): ValidationResult
    {
        $propertyInspector = new PropertyInspector(
            new AttributeAnalyzer(Validate::class)
        );
        $attributeHandler = new AttributeHandler(self::IDENTIFIER, $this->builder);
        $resultProcessor = new DefaultValidationResultProcessor();
        $handler = $propertyInspector->inspect($object, $attributeHandler);

        return $resultProcessor->process($handler);
    }
}
