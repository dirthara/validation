<?php

declare(strict_types=1);

namespace Dirthara\Validation;

final readonly class ValidationError
{
    public function __construct(
        public string $field,
        public string $message,
        public ?string $code = null,
    ) {}

    public function prefix(string $field): self
    {
        return new self(
            field: $this->field === '' ? $field : $field . '.' . $this->field,
            message: $this->message,
            code: $this->code,
        );
    }
}
