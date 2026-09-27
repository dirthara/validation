<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Presence;

use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Contract\AcceptsValue;

final readonly class Nullable implements AcceptsValue
{
    public function __construct(
        public string $message = '{input} may be null',
    ) {}

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
