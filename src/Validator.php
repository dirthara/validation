<?php

declare(strict_types=1);

namespace Dirthara\Validation;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\Contract\Validator as ValidatorContract;

final readonly class Validator implements ValidatorContract
{
    /**
     * @param array<string, list<Rule|ValidatorContract>> $rules
     */
    public function __construct(
        private array $rules,
    ) {}

    /**
     * @param array<string, mixed> $input
     */
    public function validate(array $input): ValidationResult
    {
        // ...
    }
}
