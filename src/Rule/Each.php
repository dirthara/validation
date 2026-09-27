<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Internal\RuleSet;
use Dirthara\Validation\Contract\Validator;

final readonly class Each implements Rule
{
    private RuleSet $rules;

    /**
     * @param Rule|Validator|list<Rule|Validator> $rules
     */
    public function __construct(Rule|Validator|array $rules)
    {
        $this->rules = RuleSet::from($rules);
    }

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, bool $present = true): array
    {
        if (!$present || $value === null) {
            return [];
        }

        if (!is_iterable($value)) {
            return [
                new ValidationError(field: '', message: 'The value must be iterable.', code: 'iterable'),
            ];
        }

        $errors = [];

        foreach ($value as $key => $item) {
            foreach ($this->rules->validate($item) as $error) {
                $errors[] = $error->prefix((string) $key);
            }
        }

        return $errors;
    }
}
