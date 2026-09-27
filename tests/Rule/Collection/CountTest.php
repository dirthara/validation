<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Collection;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Rule\Collection\Count;
use PHPUnit\Framework\Attributes\DataProvider;

final class CountTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public static function validValues(): iterable
    {
        yield 'list' => [[1, 2]];
        yield 'map' => [['a' => 1, 'b' => 2]];
        yield 'countable' => [new ArrayObject([1, 2])];
    }

    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_a_valid_value(mixed $value): void
    {
        self::assertSame([], new Count(2)->validate(context: new ValidationContext([]), value: $value));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidValues(): iterable
    {
        yield 'fewer' => [[1]];
        yield 'more' => [[1, 2, 3]];
        yield 'generator' => [(static function (): iterable {
            yield 1;
            yield 2;
        })()];
        yield 'string' => ['ab'];
        yield 'null' => [null];
    }

    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_an_invalid_value(mixed $value): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must contain exactly {count} items', parameters: ['count' => 2])],
            new Count(2)->validate(context: new ValidationContext([]), value: $value),
        );
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} must contain exactly {count} items', new Count(2)->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new Count(2, message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: '{input} is wrong', parameters: ['count' => 2])],
            $rule->validate(context: new ValidationContext([]), value: [1]),
        );
    }
}
