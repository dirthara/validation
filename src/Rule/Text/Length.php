<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Text;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;

use function is_string;
use function preg_match_all;

final class Length implements Rule
{
    use SkipsMissing;

    public function __construct(
        private readonly int $length,
        public readonly string $message = '{input} must be exactly {length} characters long',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        $length = is_string($value) ? preg_match_all('/./su', $value) : false;

        if ($length !== false && $length === $this->length) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['length' => $this->length])];
    }
}
