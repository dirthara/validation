<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Numeric;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;

use function is_int;
use function is_float;

final readonly class Negative implements Rule
{
    public function __construct(
        public string $message = '{input} must be negative',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        if ((is_int($value) || is_float($value)) && $value < 0) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message)];
    }
}
