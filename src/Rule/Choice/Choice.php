<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Choice;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;

use function in_array;

final class Choice implements Rule
{
    use SkipsMissing;

    /**
     * @param list<mixed> $choices
     */
    public function __construct(
        private readonly array $choices,
        public readonly string $message = '{input} must be one of {choices}',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        if (in_array($value, $this->choices, strict: true)) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['choices' => $this->choices])];
    }
}
