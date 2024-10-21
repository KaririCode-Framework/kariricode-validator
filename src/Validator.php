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
use KaririCode\Validator\Contract\ValidationResultProcessor;
use KaririCode\Validator\Processor\DefaultValidationResultProcessor;

class Validator implements ValidatorContract
{
    private const IDENTIFIER = 'validator';

    private ProcessorBuilder $builder;
    private PropertyInspector $propertyInspector;
    private AttributeHandler $attributeHandler;

    public function __construct(
        private readonly ProcessorRegistry $registry,
        private readonly ValidationResultProcessor $resultProcessor = new DefaultValidationResultProcessor()
    ) {
        $this->builder = new ProcessorBuilder($this->registry);
        $this->attributeHandler = new AttributeHandler(self::IDENTIFIER, $this->builder);
        $this->propertyInspector = new PropertyInspector(
            new AttributeAnalyzer(Validate::class)
        );
    }

    public function validate(mixed $object): ValidationResult
    {
        $handler = $this->propertyInspector->inspect($object, $this->attributeHandler);

        return $this->resultProcessor->process($handler);
    }
}
