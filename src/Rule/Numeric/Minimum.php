<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Numeric;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;

use function is_int;
use function is_float;

final readonly class Minimum implements Rule
{
    public function __construct(
        private int|float $minimum,
        public string $message = '{input} must be at least {minimum}',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        if ((is_int($value) || is_float($value)) && $value >= $this->minimum) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['minimum' => $this->minimum])];
    }
}
