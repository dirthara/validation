<?php

declare(strict_types=1);

namespace Dirthara\Validation;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\Contract\Validator as ValidatorContract;

final class ValidatorFactory
{
    /**
     * @param array<string, Rule|ValidatorContract|list<Rule|ValidatorContract>> $rules
     */
    public function create(array $rules): ValidatorContract
    {
        $normalized = array_map(static fn($fieldRules) => (
            is_array($fieldRules) ? array_values($fieldRules) : [$fieldRules]
        ), $rules);

        return new Validator($normalized);
    }
}
