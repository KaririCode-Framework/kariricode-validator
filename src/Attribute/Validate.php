<?php

declare(strict_types=1);

namespace KaririCode\Validator\Attribute;

use KaririCode\Contract\Processor\Attribute\BaseProcessorAttribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Validate extends BaseProcessorAttribute
{
}
