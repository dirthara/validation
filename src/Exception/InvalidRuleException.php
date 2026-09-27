<?php

declare(strict_types=1);

namespace Dirthara\Validation\Exception;

use Throwable;
use InvalidArgumentException;

use function sprintf;
use function get_debug_type;

class InvalidRuleException extends InvalidArgumentException implements ValidationException
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

    public static function invalidRule(mixed $rule): self
    {
        return new self(sprintf('Rule "%s" is not a valid rule.', get_debug_type($rule)))->addContext([
            'rule' => $rule,
        ]);
    }
}
