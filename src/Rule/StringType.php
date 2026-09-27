<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;

use function is_string;

final readonly class StringType implements Rule
{
    public function __construct(
        public string $message = '{input} must be a string',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        if (is_string($value)) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message)];
    }
}
