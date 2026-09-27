<?php

declare(strict_types=1);

namespace Dirthara\Validation\Exception;

use Throwable;
use RuntimeException;
use Dirthara\Validation\ValidationResult;

use function count;
use function strval;
use function implode;
use function sprintf;
use function array_map;
use function array_keys;

final class ValidationFailedException extends RuntimeException implements ValidationException
{
    use HasExceptionContext;

    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        public readonly ValidationResult $result,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        array $context = [],
    ) {
        parent::__construct($message, $code, $previous);

        $this->context = $context;
    }

    public static function forResult(ValidationResult $result): self
    {
        $fields = array_map(strval(...), array_keys($result->errorsByField()));

        return new self(
            result: $result,
            message: sprintf(
                'Validation failed for %d %s: %s.',
                count($fields),
                count($fields) === 1 ? 'field' : 'fields',
                implode(', ', array_map(self::printable(...), $fields)),
            ),
            context: ['fields' => $fields],
        );
    }
}
