<?php

declare(strict_types=1);

namespace Dirthara\Validation;

use Dirthara\Validation\Contract\Validator as ValidatorContract;

use function array_key_exists;

final readonly class Validator implements ValidatorContract
{
    /**
     * @internal
     *
     * @param array<string, RuleSet> $rules
     */
    public function __construct(
        private array $rules,
    ) {}

    /**
     * @param array<array-key, mixed> $input
     */
    public function validate(array $input): ValidationResult
    {
        $errors = [];
        $validated = [];

        foreach ($this->rules as $field => $ruleSet) {
            $value = array_key_exists($field, $input) ? $input[$field] : Missing::Value;
            $fieldErrors = $ruleSet->validate($value);

            foreach ($fieldErrors as $error) {
                $errors[] = $error->prefix($field);
            }

            if ($fieldErrors === [] && !$value instanceof Missing) {
                $validated[$field] = $value;
            }
        }

        return new ValidationResult($errors, $validated);
    }
}
