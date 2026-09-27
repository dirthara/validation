<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;

final class ValidationErrorTest extends TestCase
{
    #[Test]
    public function it_carries_its_field_message_and_code(): void
    {
        $error = new ValidationError(
            field: 'email',
            message: 'The value must be a valid email address.',
            code: 'email',
        );

        self::assertSame('email', $error->field);
        self::assertSame('The value must be a valid email address.', $error->message);
        self::assertSame('email', $error->code);
    }

    #[Test]
    public function it_has_no_code_by_default(): void
    {
        self::assertNull(new ValidationError(field: 'email', message: 'Invalid.')->code);
    }

    #[Test]
    public function it_takes_the_prefix_as_its_field_when_it_has_none(): void
    {
        $error = new ValidationError(field: '', message: 'Invalid.', code: 'invalid');
        $prefixed = $error->prefix('email');

        self::assertNotSame($error, $prefixed);
        self::assertSame('', $error->field);
        self::assertSame('email', $prefixed->field);
        self::assertSame('Invalid.', $prefixed->message);
        self::assertSame('invalid', $prefixed->code);
    }

    #[Test]
    public function it_joins_the_prefix_and_its_field_with_a_dot(): void
    {
        $error = new ValidationError(field: 'email', message: 'Invalid.')
            ->prefix('0')
            ->prefix('users');

        self::assertSame('users.0.email', $error->field);
        self::assertNull($error->code);
    }
}
