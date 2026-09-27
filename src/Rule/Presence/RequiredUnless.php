<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Presence;

use Dirthara\Validation\Missing;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Contract\ContextualRule;
use Dirthara\Validation\Contract\ValidatesMissing;

final readonly class RequiredUnless implements ContextualRule, ValidatesMissing
{
    public function __construct(
        private string|int $field,
        private mixed $value,
        public string $message = '{input} is required unless {other} is {value}',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        $required = $context->value($this->field) !== $this->value;

        if (!$required || !$value instanceof Missing && $value !== null) {
            return [];
        }

        return [
            new ValidationError(messageKey: $this->message, parameters: [
                'other' => $this->field,
                'value' => $this->value,
            ]),
        ];
    }
}
