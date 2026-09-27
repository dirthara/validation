<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Comparison;

use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Contract\ContextualRule;

final readonly class Same implements ContextualRule
{
    public function __construct(
        private string|int $field,
        public string $message = '{input} must be the same as {other}',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        if ($context->has($this->field) && $value === $context->value($this->field)) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['other' => $this->field])];
    }
}
