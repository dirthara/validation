<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Fixtures;

use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Contract\ValidatesNull;

final readonly class RejectsNullRule implements ValidatesNull
{
    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value): array
    {
        if ($value !== null) {
            return [];
        }

        return [new ValidationError(message: 'The value must not be null.', code: 'not_null')];
    }
}
