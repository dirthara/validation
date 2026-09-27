<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Presence;

use Dirthara\Validation\Missing;
use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;

final class ProhibitedWithout implements Rule
{
    public bool $validatesMissing {
        get => true;
    }

    public function __construct(
        private readonly string|int $field,
        public readonly string $message = '{input} is prohibited when {other} is not present',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        $other = $context->value($this->field);
        $condition = $other === Missing::Value || $other === null;

        if (!$condition || $value === Missing::Value) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['other' => $this->field])];
    }
}
