<?php

declare(strict_types=1);

namespace Dirthara\Validation;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\Contract\AcceptsValue;
use Dirthara\Validation\Contract\ValidatesMissing;
use Dirthara\Validation\Exception\InvalidRuleException;

use function is_array;
use function array_map;
use function array_values;

/**
 * @internal
 */
final readonly class RuleSet
{
    /**
     * @param list<Rule> $rules
     */
    private function __construct(
        private array $rules,
    ) {}

    /**
     * @param Rule|list<Rule> $rules
     */
    public static function from(Rule|array $rules): self
    {
        if (!is_array($rules)) {
            return new self([self::rule($rules)]);
        }

        return new self(array_map(self::rule(...), array_values($rules)));
    }

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        foreach ($this->rules as $rule) {
            if ($rule instanceof AcceptsValue && $rule->accepts($value)) {
                return [];
            }
        }

        foreach ($this->rules as $rule) {
            if ($value instanceof Missing && !$rule instanceof ValidatesMissing) {
                continue;
            }

            $errors = $rule->validate($value);

            if ($errors !== []) {
                return $errors;
            }
        }

        return [];
    }

    private static function rule(mixed $rule): Rule
    {
        if ($rule instanceof Rule) {
            return $rule;
        }

        throw InvalidRuleException::notARule($rule);
    }
}
