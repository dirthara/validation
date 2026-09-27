<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Presence;

use Dirthara\Validation\Missing;
use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;

final class RequiredWith implements Rule
{
    public bool $validatesMissing {
        get => true;
    }

    public function __construct(
        private readonly string $field,
        public readonly string $message = '{input} is required when {other} is present',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        $other = $context->value($this->field);
        $condition = $other !== Missing::Value && $other !== null;

        if (!$condition || $value !== Missing::Value && $value !== null) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['other' => $this->field])];
    }
}
