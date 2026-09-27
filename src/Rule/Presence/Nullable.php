<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Presence;

use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Contract\AcceptsValue;

final class Nullable implements AcceptsValue
{
    use SkipsMissing;

    public function __construct(
        public readonly string $message = '{input} may be null',
    ) {}

    public function accepts(mixed $value): bool
    {
        return $value === null;
    }

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        return [];
    }
}
