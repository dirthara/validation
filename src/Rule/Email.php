<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;

use function is_string;
use function filter_var;

use const FILTER_VALIDATE_EMAIL;

final readonly class Email implements Rule
{
    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        if (is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false) {
            return [];
        }

        return [
            new ValidationError(message: 'The value must be a valid email address.', code: 'email'),
        ];
    }
}
