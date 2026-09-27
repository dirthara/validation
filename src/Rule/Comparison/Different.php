<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Comparison;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;

final class Different implements Rule
{
    use SkipsMissing;

    public function __construct(
        private readonly string|int $field,
        public readonly string $message = '{input} must be different from {other}',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        if ($context->has($this->field) && $value !== $context->value($this->field)) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['other' => $this->field])];
    }
}
