<?php

declare(strict_types=1);

namespace Dirthara\Validation;

use Stringable;

use function strtr;
use function implode;
use function is_bool;
use function is_scalar;
use function get_debug_type;

final class ValidationError
{
    public string $field {
        get => implode('.', $this->path);
    }

    public string $message {
        get {
            $replacements = [];

            foreach ([
                'input' => $this->field === '' ? 'input' : $this->field,
                ...$this->parameters,
            ] as $name => $value) {
                $replacements['{' . $name . '}'] = self::render($value);
            }

            return strtr($this->messageKey, $replacements);
        }
    }

    /**
     * @param array<string, mixed> $parameters
     * @param list<string|int> $path
     */
    public function __construct(
        public readonly string $messageKey,
        public readonly array $parameters = [],
        public readonly array $path = [],
    ) {}

    public function prefix(string|int $segment): self
    {
        return new self(messageKey: $this->messageKey, parameters: $this->parameters, path: [$segment, ...$this->path]);
    }

    private static function render(mixed $value): string
    {
        return match (true) {
            is_bool($value) => $value ? 'true' : 'false',
            $value === null => 'null',
            is_scalar($value), $value instanceof Stringable => (string) $value,
            default => get_debug_type($value),
        };
    }
}
