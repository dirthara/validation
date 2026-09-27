<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule;

use Dirthara\Validation\RuleSet;
use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;

use function is_int;
use function is_string;
use function is_iterable;

final readonly class Each implements Rule
{
    private RuleSet $rules;

    /**
     * @param Rule|list<Rule> $rules
     */
    public function __construct(
        Rule|array $rules,
        public string $message = '{input} must be iterable',
    ) {
        $this->rules = RuleSet::from($rules);
    }

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        if (!is_iterable($value)) {
            return [new ValidationError(messageKey: $this->message)];
        }

        $errors = [];

        $index = 0;

        foreach ($value as $key => $item) {
            $segment = is_int($key) || is_string($key) ? $key : $index;

            foreach ($this->rules->validate($item) as $error) {
                $errors[] = $error->prefix($segment);
            }

            $index++;
        }

        return $errors;
    }
}
