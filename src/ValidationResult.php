<?php

declare(strict_types=1);

namespace Dirthara\Validation;

final readonly class ValidationResult
{
    /**
     * @param list<ValidationError> $errors
     */
    public function __construct(
        public array $errors,
    ) {}

    public function valid(): bool
    {
        return $this->errors === [];
    }

    public function failed(): bool
    {
        return !$this->valid();
    }

    /**
     * @return array<string, list<ValidationError>>
     */
    public function errorsByField(): array
    {
        $grouped = [];

        foreach ($this->errors as $error) {
            $grouped[$error->field][] = $error;
        }

        return $grouped;
    }
}
