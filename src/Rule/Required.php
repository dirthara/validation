<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;

final readonly class Required implements Rule
{
    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        if ($value !== null) {
            return [];
        }

        return [new ValidationError(field: '', message: 'The value is required.', code: 'required')];
    }
}
