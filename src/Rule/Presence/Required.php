<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Presence;

use Dirthara\Validation\Missing;
use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Contract\ValidatesMissing;

final readonly class Required implements Rule, ValidatesMissing
{
    public function __construct(
        public string $message = '{input} is required',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        if (!$value instanceof Missing && $value !== null) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message)];
    }
}
