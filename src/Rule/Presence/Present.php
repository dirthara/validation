<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Presence;

use Dirthara\Validation\Missing;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Contract\ValidatesMissing;

final readonly class Present implements ValidatesMissing
{
    public function __construct(
        public string $message = '{input} must be present',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        if (!$value instanceof Missing) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message)];
    }
}
