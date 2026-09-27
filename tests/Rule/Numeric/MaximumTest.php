<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Numeric;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\Numeric\Maximum;
use PHPUnit\Framework\Attributes\DataProvider;

final class MaximumTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public static function validValues(): iterable
    {
        yield 'equal' => [10];
        yield 'equal float' => [10.0];
        yield 'less' => [-3];
        yield 'less float' => [9.5];
    }

    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_a_valid_value(mixed $value): void
    {
        self::assertSame([], new Maximum(10)->validate($value));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidValues(): iterable
    {
        yield 'greater' => [11];
        yield 'greater float' => [10.1];
        yield 'numeric string' => ['10'];
        yield 'false' => [false];
        yield 'null' => [null];
    }

    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_an_invalid_value(mixed $value): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be at most {maximum}', parameters: ['maximum' => 10])],
            new Maximum(10)->validate($value),
        );
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} must be at most {maximum}', new Maximum(10)->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new Maximum(10, message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: '{input} is wrong', parameters: ['maximum' => 10])],
            $rule->validate(11),
        );
    }
}
