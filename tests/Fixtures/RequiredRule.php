<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Fixtures;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;

final readonly class RequiredRule implements Rule
{
    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, bool $present = true): array
    {
        if ($present && $value !== null) {
            return [];
        }

        return [new ValidationError(field: '', message: 'The value is required.', code: 'required')];
    }
}
