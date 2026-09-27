<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Text;

use stdClass;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\Rule\Text\Email;
use Dirthara\Validation\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

final class EmailTest extends TestCase
{
    #[Test]
    public function it_accepts_a_valid_email_address(): void
    {
        self::assertSame([], new Email()->validate('user@example.com'));
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

        self::assertEquals([new ValidationError(messageKey: '{input} must be a valid email address')], $errors);
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} must be a valid email address', new Email()->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new Email(message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals([new ValidationError(messageKey: '{input} is wrong')], $rule->validate('invalid'));
    }
}
