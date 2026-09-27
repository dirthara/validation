<?php

declare(strict_types=1);

namespace Dirthara\Validation;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\Internal\RuleSet;
use Dirthara\Validation\Contract\Validator as ValidatorContract;

final readonly class ValidatorFactory
{
    /**
     * @param array<string, Rule|ValidatorContract|list<Rule|ValidatorContract>> $rules
     */
    public function create(array $rules): Validator
    {
        $ruleSets = array_map(RuleSet::from(...), $rules);

        return new Validator($ruleSets);
    }
}
