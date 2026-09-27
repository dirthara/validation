<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Collection;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;

use function count;
use function is_countable;

final class Count implements Rule
{
    use SkipsMissing;

    public function __construct(
        private readonly int $count,
        public readonly string $message = '{input} must contain exactly {count} items',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        if (is_countable($value) && count($value) === $this->count) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['count' => $this->count])];
    }
}
