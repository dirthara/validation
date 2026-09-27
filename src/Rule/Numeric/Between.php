<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Numeric;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Exception\InvalidRuleException;

use function is_int;
use function is_float;

final class Between implements Rule
{
    use SkipsMissing;

    public function __construct(
        private readonly int|float $minimum,
        private readonly int|float $maximum,
        public readonly string $message = '{input} must be between {minimum} and {maximum}',
    ) {
        if ($this->minimum > $this->maximum) {
            throw InvalidRuleException::invalidRange($this->minimum, $this->maximum);
        }
    }

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        if ((is_int($value) || is_float($value)) && $value >= $this->minimum && $value <= $this->maximum) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: [
            'minimum' => $this->minimum,
            'maximum' => $this->maximum,
        ])];
    }
}
