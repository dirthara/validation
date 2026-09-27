<?php

declare(strict_types=1);

namespace Dirthara\Validation;

use function implode;

final readonly class ValidationError
{
    public string $field;

    /**
     * @param array<string, mixed> $parameters
     * @param list<string|int> $path
     */
    public function __construct(
        public string $message,
        public string $code,
        public array $parameters = [],
        public array $path = [],
    ) {
        $this->field = implode('.', $path);
    }

    public function prefix(string|int $segment): self
    {
        return new self(message: $this->message, code: $this->code, parameters: $this->parameters, path: [
            $segment,
            ...$this->path,
        ]);
    }
}
