<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Type;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;

use function is_array;

final class ArrayType implements Rule
{
    use SkipsMissing;

    public function __construct(
        public readonly string $message = '{input} must be an array',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        if (is_array($value)) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message)];
    }
}
