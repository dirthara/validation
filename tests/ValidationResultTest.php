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
        $errors = [new ValidationError(field: 'email', message: 'Invalid.')];

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
        $result = new ValidationResult([new ValidationError(field: 'email', message: 'Invalid.')]);

        self::assertFalse($result->valid());
        self::assertTrue($result->failed());
    }
}
