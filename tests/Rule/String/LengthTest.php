<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\String;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\String\Length;
use PHPUnit\Framework\Attributes\DataProvider;

final class LengthTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public static function validValues(): iterable
    {
        yield 'ascii' => ['abc'];
        yield 'accents' => ['äöü'];
        yield 'cjk' => ['日本語'];
    }

    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_a_valid_value(mixed $value): void
    {
        self::assertSame([], new Length(3)->validate($value));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidValues(): iterable
    {
        yield 'shorter' => ['ab'];
        yield 'longer' => ['abcd'];
        yield 'integer' => [123];
        yield 'null' => [null];
        yield 'invalid utf-8' => ["\xff\xfe\xfd"];
    }

    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_an_invalid_value(mixed $value): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be exactly {length} characters long', parameters: [
                'length' => 3,
            ])],
            new Length(3)->validate($value),
        );
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} must be exactly {length} characters long', new Length(3)->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new Length(3, message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: '{input} is wrong', parameters: ['length' => 3])],
            $rule->validate('ab'),
        );
    }
}
