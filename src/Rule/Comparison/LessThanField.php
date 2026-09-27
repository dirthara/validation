<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Comparison;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;

use function is_int;
use function is_float;

final class LessThanField implements Rule
{
    use SkipsMissing;

    public function __construct(
        private readonly string $field,
        public readonly string $message = '{input} must be less than {other}',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        $other = $context->value($this->field);

        if ((is_int($value) || is_float($value)) && (is_int($other) || is_float($other)) && $value < $other) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['other' => $this->field])];
    }
}
