<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Numeric;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;

use function is_int;
use function is_float;

final class Positive implements Rule
{
    use SkipsMissing;

    public function __construct(
        public readonly string $message = '{input} must be positive',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        if ((is_int($value) || is_float($value)) && $value > 0) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message)];
    }
}
