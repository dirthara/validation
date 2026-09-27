<?php

declare(strict_types=1);

namespace Dirthara\Validation\Contract;

use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;

interface Rule
{
    public string $message { get; }

    public bool $validatesMissing { get; }

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array;
}
