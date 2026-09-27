<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\String;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\String\MaxLength;
use PHPUnit\Framework\Attributes\DataProvider;

final class MaxLengthTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public static function validValues(): iterable
    {
        yield 'exactly' => ['abc'];
        yield 'shorter' => ['ab'];
        yield 'empty' => [''];
        yield 'multibyte' => ['äöü'];
    }

    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_a_valid_value(mixed $value): void
    {
        self::assertSame([], new MaxLength(3)->validate($value));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidValues(): iterable
    {
        yield 'longer' => ['abcd'];
        yield 'integer' => [1];
        yield 'null' => [null];
        yield 'invalid utf-8' => ["\xff"];
    }

    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_an_invalid_value(mixed $value): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be at most {maximum} characters long', parameters: [
                'maximum' => 3,
            ])],
            new MaxLength(3)->validate($value),
        );
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} must be at most {maximum} characters long', new MaxLength(3)->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new MaxLength(3, message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: '{input} is wrong', parameters: ['maximum' => 3])],
            $rule->validate('abcd'),
        );
    }
}
