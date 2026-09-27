<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule;

use stdClass;
use ArrayIterator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\Rule\ArrayType;
use Dirthara\Validation\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

final class ArrayTypeTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public static function validValues(): iterable
    {
        yield 'empty array' => [[]];
        yield 'list' => [[1, 2]];
        yield 'map' => [['a' => 1]];
    }

    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_a_valid_value(mixed $value): void
    {
        self::assertSame([], new ArrayType()->validate($value));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidValues(): iterable
    {
        yield 'iterator' => [new ArrayIterator([1])];
        yield 'string' => ['text'];
        yield 'null' => [null];
        yield 'object' => [new stdClass()];
    }

    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_an_invalid_value(mixed $value): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be an array')],
            new ArrayType()->validate($value),
        );
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} must be an array', new ArrayType()->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new ArrayType(message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: '{input} is wrong')],
            $rule->validate(new ArrayIterator([1])),
        );
    }
}
