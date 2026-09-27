<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Collection;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Exception\InvalidRuleException;

use function count;
use function is_countable;

final class MaxCount implements Rule
{
    use SkipsMissing;

    /**
     * @throws InvalidRuleException
     */
    public function __construct(
        private readonly int $maximum,
        public readonly string $message = '{input} must contain at most {maximum} items',
    ) {
        if ($this->maximum < 0) {
            throw InvalidRuleException::negativeSize(self::class, 'maximum', $this->maximum);
        }
    }

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        if (is_countable($value) && count($value) <= $this->maximum) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['maximum' => $this->maximum])];
    }
}
