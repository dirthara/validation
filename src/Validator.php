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
            $fieldErrors = $ruleSet->validate($input[$field] ?? null);

            foreach ($fieldErrors as $error) {
                $errors[] = $error->prefix($field);
            }

            if ($fieldErrors === [] && array_key_exists($field, $input)) {
                $validated[$field] = $input[$field];
            }
        }

        return new ValidationResult($errors, $validated);
    }
}
