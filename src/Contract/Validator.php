<?php

declare(strict_types=1);

namespace Dirthara\Validation\Contract;

use Dirthara\Validation\ValidationResult;

interface Validator
{
    /**
     * @param array<string, mixed> $input
     */
    public function validate(array $input): ValidationResult;
}
