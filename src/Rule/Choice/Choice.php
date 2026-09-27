<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Choice;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;

use function in_array;

final readonly class Choice implements Rule
{
    /**
     * @param list<mixed> $choices
     */
    public function __construct(
        private array $choices,
        public string $message = '{input} must be one of {choices}',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        if (in_array($value, $this->choices, strict: true)) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['choices' => $this->choices])];
    }
}
