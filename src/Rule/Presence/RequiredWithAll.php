<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule\Presence;

use Dirthara\Validation\Missing;
use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;

final class RequiredWithAll implements Rule
{
    public bool $validatesMissing {
        get => true;
    }

    /**
     * @param list<string> $fields
     */
    public function __construct(
        private readonly array $fields,
        public readonly string $message = '{input} is required when all of {others} are present',
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, ValidationContext $context): array
    {
        if ($value !== Missing::Value && $value !== null) {
            return [];
        }

        foreach ($this->fields as $field) {
            $other = $context->value($field);

            if ($other === Missing::Value || $other === null) {
                return [];
            }
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['others' => $this->fields])];
    }
}
