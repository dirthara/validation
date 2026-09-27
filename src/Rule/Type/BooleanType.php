<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Type;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;

use function is_bool;

final class BooleanType implements Rule
{
    use SkipsMissing;

    public function __construct(
        public readonly string $message = '{input} must be a boolean',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        if (is_bool($value)) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message)];
    }
}
