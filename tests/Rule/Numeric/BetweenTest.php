<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Numeric;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\Numeric\Between;
use PHPUnit\Framework\Attributes\DataProvider;
use Dirthara\Validation\Exception\InvalidRuleException;

final class BetweenTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public static function validValues(): iterable
    {
        yield 'minimum' => [1];
        yield 'maximum' => [10];
        yield 'inside' => [5];
        yield 'inside float' => [9.99];
    }

    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_a_valid_value(mixed $value): void
    {
        self::assertSame([], new Between(1, 10)->validate($value));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidValues(): iterable
    {
        yield 'below' => [0];
        yield 'above' => [11];
        yield 'above float' => [10.01];
        yield 'numeric string' => ['5'];
        yield 'null' => [null];
    }

    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_an_invalid_value(mixed $value): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be between {minimum} and {maximum}', parameters: [
                'minimum' => 1,
                'maximum' => 10,
            ])],
            new Between(1, 10)->validate($value),
        );
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} must be between {minimum} and {maximum}', new Between(1, 10)->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new Between(1, 10, message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: '{input} is wrong', parameters: ['minimum' => 1, 'maximum' => 10])],
            $rule->validate(0),
        );
    }

    #[Test]
    public function it_accepts_a_range_of_a_single_value(): void
    {
        self::assertSame([], new Between(5, 5)->validate(5));
    }

    #[Test]
    public function it_rejects_a_minimum_greater_than_its_maximum(): void
    {
        try {
            new Between(10, 1);
            self::fail('Expected an InvalidRuleException.');
        } catch (InvalidRuleException $exception) {
            self::assertSame(['minimum' => 10, 'maximum' => 1], $exception->context);
        }
    }
}
