<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Presence;

use Dirthara\Validation\Missing;
use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;

final class Present implements Rule
{
    public bool $validatesMissing {
        get => true;
    }

    public function __construct(
        public readonly string $message = '{input} must be present',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        if (!$value instanceof Missing) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message)];
    }
}
