<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Numeric;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\Numeric\Minimum;
use PHPUnit\Framework\Attributes\DataProvider;

final class MinimumTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public static function validValues(): iterable
    {
        yield 'equal' => [18];
        yield 'equal float' => [18.0];
        yield 'greater' => [100];
        yield 'greater float' => [18.5];
    }

    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_a_valid_value(mixed $value): void
    {
        self::assertSame([], new Minimum(18)->validate($value));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidValues(): iterable
    {
        yield 'less' => [17];
        yield 'less float' => [17.9];
        yield 'numeric string' => ['18'];
        yield 'true' => [true];
        yield 'null' => [null];
    }

    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_an_invalid_value(mixed $value): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be at least {minimum}', parameters: ['minimum' => 18])],
            new Minimum(18)->validate($value),
        );
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} must be at least {minimum}', new Minimum(18)->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new Minimum(18, message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: '{input} is wrong', parameters: ['minimum' => 18])],
            $rule->validate(17),
        );
    }
}
