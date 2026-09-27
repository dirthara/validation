<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Type;

use stdClass;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Rule\Type\Numeric;
use PHPUnit\Framework\Attributes\DataProvider;

final class NumericTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public static function validValues(): iterable
    {
        yield 'integer' => [18];
        yield 'float' => [1.5];
        yield 'numeric string' => ['18'];
        yield 'decimal string' => ['-1.5'];
        yield 'exponent string' => ['1e3'];
    }

    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_a_valid_value(mixed $value): void
    {
        self::assertSame([], new Numeric()->validate($value));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidValues(): iterable
    {
        yield 'text' => ['eighteen'];
        yield 'empty string' => [''];
        yield 'true' => [true];
        yield 'null' => [null];
        yield 'array' => [[18]];
        yield 'object' => [new stdClass()];
    }

    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_an_invalid_value(mixed $value): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be numeric')],
            new Numeric()->validate($value),
        );
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} must be numeric', new Numeric()->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new Numeric(message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals([new ValidationError(messageKey: '{input} is wrong')], $rule->validate('eighteen'));
    }
}
