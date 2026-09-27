<?php

declare(strict_types=1);

namespace Dirthara\Validation\Internal;

use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Contract\Validator;

use function is_array;

/**
 * @internal
 */
final readonly class NestedValidator implements Rule
{
    public function __construct(
        private Validator $validator,
    ) {}

    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, bool $present = true): array
    {
        if (!$present || $value === null) {
            return [];
        }

        if (!is_array($value)) {
            return [new ValidationError(field: '', message: 'The value must be an array.', code: 'array')];
        }

        // @mago-expect analysis:less-specific-argument The validator looks its fields up by key, so integer keys are harmless
        return $this->validator->validate($value)->errors;
    }
}
