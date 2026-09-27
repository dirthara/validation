<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Presence;

use Dirthara\Validation\Missing;
use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;

final class ProhibitedUnless implements Rule
{
    public bool $validatesMissing {
        get => true;
    }

    public function __construct(
        private readonly string|int $field,
        private readonly mixed $value,
        public readonly string $message = '{input} is prohibited unless {other} is {value}',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        $other = $context->value($this->field);
        $condition = !$context->has($this->field) || $other !== $this->value;

        if (!$condition || $value === Missing::Value) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: [
            'other' => $this->field,
            'value' => $this->value,
        ])];
    }
}
