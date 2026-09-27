<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\String;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;

use function is_string;
use function preg_match;

final readonly class Uuid implements Rule
{
    public function __construct(
        public string $message = '{input} must be a valid UUID',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        if (
            is_string($value)
            && preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/iD', $value) === 1
        ) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message)];
    }
}
