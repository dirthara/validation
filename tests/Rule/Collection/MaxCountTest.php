<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Collection;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;
use PHPUnit\Framework\Attributes\DataProvider;
use Dirthara\Validation\Rule\Collection\MaxCount;
use Dirthara\Validation\Exception\InvalidRuleException;

final class MaxCountTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public static function validValues(): iterable
    {
        yield 'exactly' => [[1, 2]];
        yield 'fewer' => [[1]];
        yield 'empty' => [[]];
        yield 'countable' => [new ArrayObject([1])];
    }

    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_a_valid_value(mixed $value): void
    {
        self::assertSame([], new MaxCount(2)->validate(context: new ValidationContext([]), value: $value));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidValues(): iterable
    {
        yield 'more' => [[1, 2, 3]];
        yield 'generator' => [(static function (): iterable {
            yield 1;
            yield 2;
        })()];
        yield 'string' => ['a'];
        yield 'null' => [null];
    }

    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_an_invalid_value(mixed $value): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must contain at most {maximum} items', parameters: [
                'maximum' => 2,
            ])],
            new MaxCount(2)->validate(context: new ValidationContext([]), value: $value),
        );
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} must contain at most {maximum} items', new MaxCount(2)->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new MaxCount(2, message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: '{input} is wrong', parameters: ['maximum' => 2])],
            $rule->validate(context: new ValidationContext([]), value: [1, 2, 3]),
        );
    }

    #[Test]
    public function it_accepts_a_zero_size(): void
    {
        self::assertSame([], new MaxCount(0)->validate([], new ValidationContext([])));
    }

    #[Test]
    public function it_rejects_a_negative_size_during_construction(): void
    {
        try {
            new MaxCount(-1);
            self::fail('Expected invalid rule configuration.');
        } catch (InvalidRuleException $exception) {
            self::assertSame(
                [
                    'rule' => MaxCount::class,
                    'parameter' => 'maximum',
                    'value' => -1,
                ],
                $exception->context,
            );
            self::assertSame('maximum must be zero or greater for ' . MaxCount::class . '.', $exception->getMessage());
        }
    }
}
