<?php

declare(strict_types=1);

namespace Dirthara\Validation;

use function implode;

final class ValidationError
{
    public string $field {
        get => implode('.', $this->path);
    }

    /**
     * @param array<string, mixed> $parameters
     * @param list<string|int> $path
     */
    public function __construct(
        public readonly string $message,
        public readonly string $code,
        public readonly array $parameters = [],
        public readonly array $path = [],
    ) {}

    public function prefix(string|int $segment): self
    {
        return new self(message: $this->message, code: $this->code, parameters: $this->parameters, path: [
            $segment,
            ...$this->path,
        ]);
    }
}
