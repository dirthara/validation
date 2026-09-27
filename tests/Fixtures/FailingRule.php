<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Fixtures;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;

final readonly class FailingRule implements Rule
{
    public function __construct(
        private string $field = '',
        public string $message = '{input} failed',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        return [new ValidationError(messageKey: $this->message, path: $this->field === '' ? [] : [$this->field])];
    }
}
