<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Type;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;

use function is_iterable;

final readonly class IterableType implements Rule
{
    public function __construct(
        public string $message = '{input} must be iterable',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        if (is_iterable($value)) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message)];
    }
}
