<?php

declare(strict_types=1);

namespace Dirthara\Validation\Contract;

use Dirthara\Validation\ValidationError;

interface Rule
{
    public string $message { get; }

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array;
}
