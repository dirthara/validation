<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Collection;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;

use function count;
use function is_countable;

final readonly class MaxCount implements Rule
{
    public function __construct(
        private int $maximum,
        public string $message = '{input} must contain at most {maximum} items',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        if (is_countable($value) && count($value) <= $this->maximum) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['maximum' => $this->maximum])];
    }
}
