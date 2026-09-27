<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Fixtures;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;

final readonly class FailingRule implements Rule
{
    public function __construct(
        private string $field = '',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        return [new ValidationError(
            message: 'The value failed.',
            code: 'failing',
            path: $this->field === '' ? [] : [$this->field],
        )];
    }
}
