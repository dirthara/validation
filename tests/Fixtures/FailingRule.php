<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Fixtures;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;

final class FailingRule implements Rule
{
    use SkipsMissing;

    public function __construct(
        private readonly string $field = '',
        public readonly string $message = '{input} failed',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        return [new ValidationError(messageKey: $this->message, path: $this->field === '' ? [] : [$this->field])];
    }
}
