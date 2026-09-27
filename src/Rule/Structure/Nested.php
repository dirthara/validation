<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Structure;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Contract\Validator;

use function is_array;

final class Nested implements Rule
{
    use SkipsMissing;

    public function __construct(
        private readonly Validator $validator,
        public readonly string $message = '{input} must be an array',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        if (!is_array($value)) {
            return [new ValidationError(messageKey: $this->message)];
        }

        return $this->validator->validate($value)->errors;
    }
}
