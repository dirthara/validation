<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Fixtures;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\ValidationContext;

use function array_keys;

final class ContextFieldsRule implements Rule
{
    use SkipsMissing;

    public function __construct(
        public readonly string $message = '{input} saw {fields}',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        return [new ValidationError(messageKey: $this->message, parameters: ['fields' => array_keys($context->input)])];
    }
}
