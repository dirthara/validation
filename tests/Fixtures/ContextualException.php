<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Fixtures;

use RuntimeException;
use Dirthara\Validation\Exception\HasExceptionContext;
use Dirthara\Validation\Exception\ValidationException;

final class ContextualException extends RuntimeException implements ValidationException
{
    use HasExceptionContext;

    public static function describe(string $value): string
    {
        return self::printable($value);
    }
}
