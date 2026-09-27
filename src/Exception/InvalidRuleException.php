<?php

declare(strict_types=1);

namespace Dirthara\Validation\Exception;

use Throwable;
use InvalidArgumentException;

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
        return new self(sprintf('Rule "%s" is not a valid rule.', gettype($rule)))->addContext(['rule' => $rule]);
    }
}
