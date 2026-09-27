<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Numeric;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Exception\InvalidRuleException;

use function is_int;
use function is_float;

final readonly class Between implements Rule
{
    public function __construct(
        private int|float $minimum,
        private int|float $maximum,
        public string $message = '{input} must be between {minimum} and {maximum}',
    ) {
        if ($this->minimum > $this->maximum) {
            throw InvalidRuleException::invalidRange($this->minimum, $this->maximum);
        }
    }

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
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
