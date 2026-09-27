<?php

declare(strict_types=1);

namespace Dirthara\Validation;

use function array_key_exists;

final readonly class ValidationContext
{
    /**
     * @param array<array-key, mixed> $input
     */
    public function __construct(
        public array $input,
    ) {}

    public function has(string|int $field): bool
    {
        return array_key_exists($field, $this->input);
    }

    public function value(string|int $field): mixed
    {
        return $this->has($field) ? $this->input[$field] : Missing::Value;
    }
}
