<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Text;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\Rule\Text\Regex;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;
use PHPUnit\Framework\Attributes\DataProvider;
use Dirthara\Validation\Exception\InvalidRuleException;

final class RegexTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public static function validValues(): iterable
    {
        yield 'lowercase' => ['abc'];
        yield 'single letter' => ['a'];
    }

    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_a_valid_value(mixed $value): void
    {
        self::assertSame([], new Regex('/^[a-z]+$/')->validate(context: new ValidationContext([]), value: $value));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidValues(): iterable
    {
        yield 'digits' => ['abc1'];
        yield 'uppercase' => ['ABC'];
        yield 'empty' => [''];
        yield 'integer' => [42];
        yield 'null' => [null];
    }

    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_an_invalid_value(mixed $value): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} has an invalid format', parameters: ['pattern' => '/^[a-z]+$/'])],
            new Regex('/^[a-z]+$/')->validate(context: new ValidationContext([]), value: $value),
        );
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} has an invalid format', new Regex('/^[a-z]+$/')->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new Regex('/^[a-z]+$/', message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: '{input} is wrong', parameters: ['pattern' => '/^[a-z]+$/'])],
            $rule->validate(context: new ValidationContext([]), value: 'abc1'),
        );
    }

    #[Test]
    public function it_rejects_an_invalid_pattern(): void
    {
        try {
            new Regex('/[a-z');
            self::fail('Expected an InvalidRuleException.');
        } catch (InvalidRuleException $exception) {
            self::assertSame(['pattern' => '/[a-z'], $exception->context);
        }
    }
}
