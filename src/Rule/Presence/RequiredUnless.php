<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Presence;

use Dirthara\Validation\Missing;
use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;

final class RequiredUnless implements Rule
{
    public bool $validatesMissing {
        get => true;
    }

    public function __construct(
        private readonly string $field,
        private readonly mixed $value,
        public readonly string $message = '{input} is required unless {other} is {value}',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        $required = !$context->has($this->field) || $context->value($this->field) !== $this->value;

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
