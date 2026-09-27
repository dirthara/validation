<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationResult;

final class ValidationResultTest extends TestCase
{
    #[Test]
    public function it_carries_its_errors(): void
    {
        $errors = [new ValidationError(messageKey: 'Invalid.', path: ['email'])];

        self::assertSame($errors, new ValidationResult($errors)->errors);
        self::assertSame([], new ValidationResult([])->errors);
    }

    #[Test]
    public function it_is_valid_without_errors(): void
    {
        $result = new ValidationResult([]);

        self::assertTrue($result->valid());
        self::assertFalse($result->failed());
    }

    #[Test]
    public function it_fails_with_errors(): void
    {
        $result = new ValidationResult([new ValidationError(messageKey: 'Invalid.', path: ['email'])]);

        self::assertFalse($result->valid());
        self::assertTrue($result->failed());
    }

    #[Test]
    public function it_groups_its_errors_by_field(): void
    {
        $email = new ValidationError(messageKey: 'Invalid.', path: ['email']);
        $required = new ValidationError(messageKey: 'Required.', path: ['users', 0, 'name']);
        $other = new ValidationError(messageKey: 'Invalid.', path: ['email']);

        self::assertSame(
            ['email' => [$email, $other], 'users.0.name' => [$required]],
            new ValidationResult([$email, $required, $other])->errorsByField(),
        );
        self::assertSame([], new ValidationResult([])->errorsByField());

        $first = new ValidationError(messageKey: '{input} is required', path: [0]);
        self::assertSame([0 => [$first]], new ValidationResult([$first])->errorsByField());
    }

    #[Test]
    public function it_groups_colliding_rendered_fields_and_preserves_structural_paths(): void
    {
        $literal = new ValidationError('{input} is required', path: ['address.street']);
        $nested = new ValidationError('{input} is required', path: ['address', 'street']);
        $result = new ValidationResult([$literal, $nested]);

        self::assertSame('address.street', $literal->field);
        self::assertSame($literal->field, $nested->field);
        self::assertSame(['address.street' => [$literal, $nested]], $result->errorsByField());
        self::assertSame(['address.street'], $literal->path);
        self::assertSame(['address', 'street'], $nested->path);
    }
}
