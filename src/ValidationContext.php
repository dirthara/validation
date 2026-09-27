<?php

declare(strict_types=1);

namespace Dirthara\Validation;

use function array_key_exists;

final readonly class ValidationContext
{
    /**
     * @param array<string, mixed> $input
     */
    public function __construct(
        public array $input,
    ) {}

    public function has(string $field): bool
    {
        return array_key_exists($field, $this->input);
    }

    public function value(string $field): mixed
    {
        return $this->has($field) ? $this->input[$field] : Missing::Value;
    }
}
