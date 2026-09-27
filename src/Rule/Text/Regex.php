<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Text;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Exception\InvalidRuleException;

use function is_string;
use function preg_match;

final class Regex implements Rule
{
    use SkipsMissing;

    public function __construct(
        private readonly string $pattern,
        public readonly string $message = '{input} has an invalid format',
    ) {
        // @mago-expect lint:no-error-control-operator An invalid pattern is reported as an InvalidRuleException instead
        if (@preg_match($this->pattern, subject: '') === false) {
            throw InvalidRuleException::invalidPattern($this->pattern);
        }
    }

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        if (is_string($value) && preg_match($this->pattern, $value) === 1) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['pattern' => $this->pattern])];
    }
}
