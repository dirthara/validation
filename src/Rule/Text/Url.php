<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Text;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;

use function is_string;
use function filter_var;

use const FILTER_VALIDATE_URL;

final class Url implements Rule
{
    use SkipsMissing;

    public function __construct(
        public readonly string $message = '{input} must be a valid URL',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        if (is_string($value) && filter_var($value, FILTER_VALIDATE_URL) !== false) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message)];
    }
}
