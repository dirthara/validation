<?php

declare(strict_types=1);

namespace Dirthara\Validation\Exception;

use Throwable;
use InvalidArgumentException;

use function sprintf;
use function get_debug_type;

final class InvalidRuleException extends InvalidArgumentException implements ValidationException
{
    use HasExceptionContext;

    /**
     * @param array<string, mixed> $context
     */
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous);

        $this->context = $context;
    }

    public static function notARule(mixed $rule): self
    {
        return new self(message: sprintf('Rule "%s" is not a valid rule.', get_debug_type($rule)), context: [
            'rule' => $rule,
        ]);
    }

    public static function invalidPattern(string $pattern): self
    {
        return new self(
            message: sprintf('Pattern "%s" is not a valid regular expression.', self::printable($pattern)),
            context: ['pattern' => $pattern],
        );
    }

    public static function invalidRange(int|float $minimum, int|float $maximum): self
    {
        return new self(message: sprintf('Minimum %s is greater than maximum %s.', $minimum, $maximum), context: [
            'minimum' => $minimum,
            'maximum' => $maximum,
        ]);
    }
}
