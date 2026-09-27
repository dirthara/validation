<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Contract\Validator;

use function is_array;

final readonly class Nested implements Rule
{
    public function __construct(
        private Validator $validator,
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        if (!is_array($value)) {
            return [new ValidationError(message: 'The value must be an array.', code: 'array')];
        }

        return $this->validator->validate($value)->errors;
    }
}
