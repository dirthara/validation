<?php

declare(strict_types=1);

namespace Dirthara\Validation;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\Contract\AcceptsValue;
use Dirthara\Validation\Contract\ContextualRule;
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
     * @param list<Rule|ContextualRule> $rules
     */
    private function __construct(
        private array $rules,
    ) {}

    /**
     * @param Rule|ContextualRule|list<Rule|ContextualRule> $rules
     */
    public static function from(Rule|ContextualRule|array $rules): self
    {
        if (!is_array($rules)) {
            return new self([self::rule($rules)]);
        }

        return new self(array_map(self::rule(...), array_values($rules)));
    }

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
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

            // @mago-expect analysis:too-many-arguments A rule that is also contextual has a validate() that accepts the context
            $errors = $rule instanceof ContextualRule ? $rule->validate($value, $context) : $rule->validate($value);

            if ($errors !== []) {
                return $errors;
            }
        }

        return [];
    }

    private static function rule(mixed $rule): Rule|ContextualRule
    {
        if ($rule instanceof Rule || $rule instanceof ContextualRule) {
            return $rule;
        }

        throw InvalidRuleException::notARule($rule);
    }
}
