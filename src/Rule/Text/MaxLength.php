<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Text;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Exception\InvalidRuleException;

use function is_string;
use function preg_match_all;

final class MaxLength implements Rule
{
    use SkipsMissing;

    /**
     * @throws InvalidRuleException
     */
    public function __construct(
        private readonly int $maximum,
        public readonly string $message = '{input} must be at most {maximum} characters long',
    ) {
        if ($this->maximum < 0) {
            throw InvalidRuleException::negativeSize(self::class, 'maximum', $this->maximum);
        }
    }

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        $length = is_string($value) ? preg_match_all('/./su', $value) : false;

        if ($length !== false && $length <= $this->maximum) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['maximum' => $this->maximum])];
    }
}
