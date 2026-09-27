<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule;

use Dirthara\Validation\Missing;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Contract\ValidatesMissing;

final readonly class Present implements ValidatesMissing
{
    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        if (!$value instanceof Missing) {
            return [];
        }

        return [new ValidationError(message: 'The value must be present.', code: 'present')];
    }
}
