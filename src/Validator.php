<?php

declare(strict_types=1);

namespace Dirthara\Validation;

use Dirthara\Validation\Contract\Validator as ValidatorContract;

final readonly class Validator implements ValidatorContract
{
    /**
     * @internal
     *
     * @param array<array-key, RuleSet> $rules
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
        $context = new ValidationContext($input);

        foreach ($this->rules as $field => $ruleSet) {
            $value = $context->value($field);

            foreach ($ruleSet->validate($value, $context) as $error) {
                $errors[] = $error->prefix($field);
            }
        }

        return new ValidationResult($errors);
    }
}
