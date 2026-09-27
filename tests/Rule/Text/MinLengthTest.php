<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Text;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Rule\Text\MinLength;
use PHPUnit\Framework\Attributes\DataProvider;

final class MinLengthTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public static function validValues(): iterable
    {
        yield 'exactly' => ['abc'];
        yield 'longer' => ['abcd'];
        yield 'accents' => ['äöü'];
    }

    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_a_valid_value(mixed $value): void
    {
        self::assertSame([], new MinLength(3)->validate(context: new ValidationContext([]), value: $value));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidValues(): iterable
    {
        yield 'shorter' => ['ab'];
        yield 'multibyte shorter' => ['äö'];
        yield 'integer' => [1234];
        yield 'null' => [null];
        yield 'invalid utf-8' => ["\xff\xfe\xfd\xfc"];
    }

    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_an_invalid_value(mixed $value): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be at least {minimum} characters long', parameters: [
                'minimum' => 3,
            ])],
            new MinLength(3)->validate(context: new ValidationContext([]), value: $value),
        );
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} must be at least {minimum} characters long', new MinLength(3)->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new MinLength(3, message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: '{input} is wrong', parameters: ['minimum' => 3])],
            $rule->validate(context: new ValidationContext([]), value: 'ab'),
        );
    }
}
