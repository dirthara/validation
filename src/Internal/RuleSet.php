<?php

declare(strict_types=1);

namespace Dirthara\Validation\Internal;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Contract\Validator;
use Dirthara\Validation\Exception\InvalidRuleException;

use function is_array;
use function array_map;
use function array_push;
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
     * @param Rule|Validator|list<Rule|Validator> $rules
     */
    public static function from(Rule|Validator|array $rules): self
    {
        if (!is_array($rules)) {
            return new self([self::rule($rules)]);
        }

        return new self(array_map(self::rule(...), array_values($rules)));
    }

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, bool $present = true): array
    {
        $errors = [];

        foreach ($this->rules as $rule) {
            array_push($errors, ...$rule->validate($value, $present));
        }

        return $errors;
    }

    /**
     * A nested validator becomes a rule, so the set only ever holds rules. The list type of from() is only a docblock,
     * so anything else is rejected here.
     */
    private static function rule(mixed $rule): Rule
    {
        if ($rule instanceof Rule) {
            return $rule;
        }

        if ($rule instanceof Validator) {
            return new NestedValidator($rule);
        }

        throw InvalidRuleException::invalidRule($rule);
    }
}
