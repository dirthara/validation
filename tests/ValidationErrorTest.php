<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;

final class ValidationErrorTest extends TestCase
{
    #[Test]
    public function it_carries_its_message_code_parameters_and_path(): void
    {
        $error = new ValidationError(
            message: 'The value must be at least 3 characters.',
            code: 'min',
            parameters: ['min' => 3],
            path: ['users', 0, 'name'],
        );

        self::assertSame('The value must be at least 3 characters.', $error->message);
        self::assertSame('min', $error->code);
        self::assertSame(['min' => 3], $error->parameters);
        self::assertSame(['users', 0, 'name'], $error->path);
        self::assertSame('users.0.name', $error->field);
    }

    #[Test]
    public function it_has_no_parameters_and_no_path_by_default(): void
    {
        $error = new ValidationError(message: 'Invalid.', code: 'invalid');

        self::assertSame([], $error->parameters);
        self::assertSame([], $error->path);
        self::assertSame('', $error->field);
    }

    #[Test]
    public function it_prepends_a_prefix_to_its_path(): void
    {
        $error = new ValidationError(message: 'Invalid.', code: 'invalid', parameters: ['max' => 5]);
        $prefixed = $error->prefix(0)->prefix('users');

        self::assertNotSame($error, $prefixed);
        self::assertSame([], $error->path);
        self::assertSame(['users', 0], $prefixed->path);
        self::assertSame('users.0', $prefixed->field);
        self::assertSame('Invalid.', $prefixed->message);
        self::assertSame('invalid', $prefixed->code);
        self::assertSame(['max' => 5], $prefixed->parameters);
    }

    #[Test]
    public function it_keeps_a_segment_that_contains_a_dot_intact_in_its_path(): void
    {
        $error = new ValidationError(message: 'Invalid.', code: 'invalid', path: ['a.b'])->prefix('settings');

        self::assertSame(['settings', 'a.b'], $error->path);
        self::assertSame('settings.a.b', $error->field);
    }
}
