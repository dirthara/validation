<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Exception;

use RuntimeException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationResult;
use Dirthara\Validation\Exception\ValidationException;
use Dirthara\Validation\Exception\ValidationFailedException;

final class ValidationFailedExceptionTest extends TestCase
{
    #[Test]
    public function it_carries_its_result_and_nothing_else_by_default(): void
    {
        $result = new ValidationResult([]);
        $exception = new ValidationFailedException($result);

        self::assertInstanceOf(ValidationException::class, $exception);
        self::assertInstanceOf(RuntimeException::class, $exception);
        self::assertSame($result, $exception->result);
        self::assertSame('', $exception->getMessage());
        self::assertSame(0, $exception->getCode());
        self::assertNull($exception->getPrevious());
        self::assertSame([], $exception->context);
    }

    #[Test]
    public function it_keeps_a_previous_exception_and_its_context(): void
    {
        $previous = new RuntimeException('cause');
        $exception = new ValidationFailedException(new ValidationResult([]), 'message', 3, $previous, ['a' => 1]);

        self::assertSame('message', $exception->getMessage());
        self::assertSame(3, $exception->getCode());
        self::assertSame($previous, $exception->getPrevious());
        self::assertSame(['a' => 1], $exception->context);
    }

    #[Test]
    public function it_names_the_fields_that_failed_without_their_values(): void
    {
        $result = new ValidationResult([
            new ValidationError(messageKey: 'Invalid.', path: ['email']),
            new ValidationError(messageKey: 'Required.', path: ['users', 0, 'name']),
            new ValidationError(messageKey: 'Invalid.', path: ['email']),
        ]);

        $exception = ValidationFailedException::forResult($result);

        self::assertSame($result, $exception->result);
        self::assertSame('Validation failed for 2 fields: email, users.0.name.', $exception->getMessage());
        self::assertSame(['fields' => ['email', 'users.0.name']], $exception->context);
    }

    #[Test]
    public function it_names_a_single_field_and_escapes_control_characters(): void
    {
        $result = new ValidationResult([new ValidationError(messageKey: 'Invalid.', path: ["e\nmail"])]);

        $exception = ValidationFailedException::forResult($result);

        self::assertSame('Validation failed for 1 field: e\\nmail.', $exception->getMessage());
        self::assertSame(['fields' => ["e\nmail"]], $exception->context);
    }

    #[Test]
    public function it_names_a_field_with_an_integer_key_as_a_string(): void
    {
        $result = new ValidationResult([new ValidationError(messageKey: 'Invalid.', path: [0])]);

        self::assertSame(['fields' => ['0']], ValidationFailedException::forResult($result)->context);
    }
}
