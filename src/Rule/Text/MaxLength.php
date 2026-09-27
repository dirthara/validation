<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Text;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;

use function is_string;
use function preg_match_all;

final readonly class MaxLength implements Rule
{
    public function __construct(
        private int $maximum,
        public string $message = '{input} must be at most {maximum} characters long',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        $length = is_string($value) ? preg_match_all('/./su', $value) : false;

        if ($length !== false && $length <= $this->maximum) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['maximum' => $this->maximum])];
    }
}
