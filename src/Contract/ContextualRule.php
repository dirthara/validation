<?php

declare(strict_types=1);

namespace Dirthara\Validation\Contract;

use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;

interface ContextualRule
{
    public string $message { get; }

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array;
}
