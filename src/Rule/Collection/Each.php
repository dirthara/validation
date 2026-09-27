<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Collection;

use Dirthara\Validation\RuleSet;
use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Contract\ContextualRule;

use function is_int;
use function is_string;
use function is_iterable;

final readonly class Each implements ContextualRule
{
    private RuleSet $rules;

    /**
     * @param Rule|ContextualRule|list<Rule|ContextualRule> $rules
     */
    public function __construct(
        Rule|ContextualRule|array $rules,
        public string $message = '{input} must be iterable',
    ) {
        $this->rules = RuleSet::from($rules);
    }

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        if (!is_iterable($value)) {
            return [new ValidationError(messageKey: $this->message)];
        }

        $errors = [];

        $index = 0;

        foreach ($value as $key => $item) {
            $segment = is_int($key) || is_string($key) ? $key : $index;

            foreach ($this->rules->validate($item, $context) as $error) {
                $errors[] = $error->prefix($segment);
            }

            $index++;
        }

        return $errors;
    }
}
