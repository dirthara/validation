<?php

declare(strict_types=1);

namespace Dirthara\Validation;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\Exception\InvalidRuleException;
use Dirthara\Validation\Contract\Validator as ValidatorContract;

use function is_string;

final readonly class ValidatorFactory
{
    /**
     * @param array<string, Rule|list<Rule>> $rules
     *
     * @throws InvalidRuleException
     */
    public function create(array $rules): ValidatorContract
    {
        $ruleSets = [];

        /** @var array<array-key, Rule|list<Rule>> $definitions */
        $definitions = $rules;

        foreach ($definitions as $field => $fieldRules) {
            if (!is_string($field)) {
                throw InvalidRuleException::invalidFieldName($field);
            }

            try {
                $ruleSets[$field] = RuleSet::from($fieldRules);
            } catch (InvalidRuleException $exception) {
                throw $exception->addContext(['field' => $field]);
            }
        }

        return new Validator($ruleSets);
    }
}
