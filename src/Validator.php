<?php

declare(strict_types=1);

namespace KaririCode\Validator;

use KaririCode\Contract\Processor\ProcessorRegistry;
use KaririCode\Contract\Validator\Validator as ValidatorContract;
use KaririCode\ProcessorPipeline\ProcessorBuilder;
use KaririCode\PropertyInspector\AttributeAnalyzer;
use KaririCode\PropertyInspector\AttributeHandler;
use KaririCode\PropertyInspector\Contract\PropertyAttributeHandler;
use KaririCode\PropertyInspector\Contract\PropertyChangeApplier;
use KaririCode\PropertyInspector\Exception\PropertyInspectionException;
use KaririCode\PropertyInspector\Utility\PropertyInspector;
use KaririCode\Validator\Attribute\Validate;

class Validator implements ValidatorContract
{
    private const IDENTIFIER = 'validator';

    private ProcessorBuilder $builder;
    private PropertyInspector $propertyInspector;
    private PropertyAttributeHandler&PropertyChangeApplier $attributeHandler;

    public function __construct(private readonly ProcessorRegistry $registry)
    {
        $this->builder = new ProcessorBuilder($this->registry);
        $this->attributeHandler = new AttributeHandler(self::IDENTIFIER, $this->builder);
        $this->propertyInspector = new PropertyInspector(
            new AttributeAnalyzer(Validate::class)
        );
    }

    public function validate(mixed $object): array
    {
        try {
            var_dump($object, $this->attributeHandler);
            $validationResults = $this->propertyInspector->inspect($object, $this->attributeHandler);

            $errors = [];
            foreach ($validationResults as $property => $result) {
                if (false === $result) {
                    $errors[$property] = ["Validation failed for {$property}"];
                } elseif (is_string($result)) {
                    $errors[$property] = [$result];
                }
            }

            return $errors;
        } catch (PropertyInspectionException $e) {
            return ['__exception' => [$e->getMessage()]];
        }
    }
}
