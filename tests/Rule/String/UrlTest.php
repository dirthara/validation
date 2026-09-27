<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\String;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\Rule\String\Url;
use Dirthara\Validation\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

final class UrlTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public static function validValues(): iterable
    {
        yield 'https' => ['https://example.com'];
        yield 'path and query' => ['https://example.com/a/b?c=d#e'];
        yield 'ftp' => ['ftp://example.com/file.txt'];
    }

    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_a_valid_value(mixed $value): void
    {
        self::assertSame([], new Url()->validate($value));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidValues(): iterable
    {
        yield 'no scheme' => ['example.com'];
        yield 'text' => ['not a url'];
        yield 'empty' => [''];
        yield 'integer' => [42];
        yield 'null' => [null];
    }

    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_an_invalid_value(mixed $value): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be a valid URL')],
            new Url()->validate($value),
        );
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} must be a valid URL', new Url()->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new Url(message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals([new ValidationError(messageKey: '{input} is wrong')], $rule->validate('example.com'));
    }
}
