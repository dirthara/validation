<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule;

use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Contract\AcceptsValue;

final readonly class Nullable implements AcceptsValue
{
    public function accepts(mixed $value): bool
    {
        return $value === null;
    }

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        return [];
    }
}
