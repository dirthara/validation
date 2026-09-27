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

    public static function invalidFieldName(int $field): self
    {
        return new self(message: 'Validator field names must be strings.', context: ['field' => $field]);
    }

    public static function negativeSize(string $rule, string $parameter, int $value): self
    {
        return new self(message: sprintf('%s must be zero or greater for %s.', $parameter, $rule), context: [
            'rule' => $rule,
            'parameter' => $parameter,
            'value' => $value,
        ]);
    }

    public static function emptyFields(string $rule): self
    {
        return new self(message: sprintf('%s requires at least one referenced field.', $rule), context: [
            'rule' => $rule,
        ]);
    }

    public static function invalidFieldReference(string $rule, mixed $field): self
    {
        return new self(message: sprintf('Referenced fields must be strings for %s.', $rule), context: [
            'rule' => $rule,
            'fieldType' => get_debug_type($field),
        ]);
    }
}
