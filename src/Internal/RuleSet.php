<?php

declare(strict_types=1);

namespace Dirthara\Validation\Internal;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Contract\Validator;
use Dirthara\Validation\Exception\InvalidRuleException;

/**
 * @internal
 */
final readonly class RuleSet
{
    /**
     * @param list<Rule|Validator> $rules
     */
    private function __construct(
        private array $rules,
    ) {}

    /**
     * @param Rule|Validator|list<Rule|Validator> $rules
     */
    public static function from(Rule|Validator|array $rules): self
    {
        if ($rules instanceof Rule || $rules instanceof Validator) {
            return new self([$rules]);
        }

        foreach ($rules as $rule) {
            if (!$rule instanceof Rule && !$rule instanceof Validator) {
                throw InvalidRuleException::invalidRule($rule);
            }
        }

        return new self(array_values($rules));
    }

    /**
     * @return list<Rule|Validator>
     */
    public function all(): array
    {
        return $this->rules;
    }

    public function validate(mixed $value, bool $present = true): array
    {
        $errors = [];

        foreach ($this->rules as $rule) {
            if ($rule instanceof Rule) {
                array_push($errors, ...$rule->validate($value, $present));

                continue;
            }

            if (!$present || $value === null) {
                continue;
            }

            if (!is_array($value)) {
                $errors[] = new ValidationError(field: '', message: 'The value must be an array.', code: 'array');

                continue;
            }

            foreach ($rule->validate($value)->errors() as $error) {
                $errors[] = $error;
            }
        }

        return $errors;
    }
}
