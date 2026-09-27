<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule;

use stdClass;
use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Rule\Email;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

final class EmailTest extends TestCase
{
    #[Test]
    public function it_accepts_a_valid_email_address(): void
    {
        self::assertSame([], new Email()->validate('user@example.com'));
    }

    #[Test]
    public function it_skips_a_missing_or_null_value(): void
    {
        self::assertSame([], new Email()->validate('not an email', present: false));
        self::assertSame([], new Email()->validate(null));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidValues(): iterable
    {
        yield 'no at sign' => ['user.example.com'];
        yield 'no domain' => ['user@'];
        yield 'empty string' => [''];
        yield 'integer' => [42];
        yield 'array' => [['user@example.com']];
        yield 'object' => [new stdClass()];
    }

    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_anything_else(mixed $value): void
    {
        $errors = new Email()->validate($value);

        self::assertEquals(
            [new ValidationError(field: '', message: 'The value must be a valid email address.', code: 'email')],
            $errors,
        );
    }
}
