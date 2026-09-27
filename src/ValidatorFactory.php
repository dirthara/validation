<?php

declare(strict_types=1);

namespace Dirthara\Validation;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\Internal\RuleSet;
use Dirthara\Validation\Exception\InvalidRuleException;
use Dirthara\Validation\Contract\Validator as ValidatorContract;

final readonly class ValidatorFactory
{
    /**
     * @param array<string, Rule|list<Rule>> $rules
     */
    public function create(array $rules): ValidatorContract
    {
        $ruleSets = [];

        foreach ($rules as $field => $fieldRules) {
            try {
                $ruleSets[$field] = RuleSet::from($fieldRules);
            } catch (InvalidRuleException $exception) {
                throw $exception->addContext(['field' => $field]);
            }
        }

        return new Validator($ruleSets);
    }
}
