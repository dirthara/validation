<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Collection;

use Dirthara\Validation\RuleSet;
use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;

use function is_int;
use function is_string;
use function is_iterable;

final class Each implements Rule
{
    use SkipsMissing;

    private readonly RuleSet $rules;

    /**
     * @param Rule|list<Rule> $rules
     */
    public function __construct(
        Rule|array $rules,
        public readonly string $message = '{input} must be iterable',
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
