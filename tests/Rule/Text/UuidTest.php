<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Text;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\Rule\Text\Uuid;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;
use PHPUnit\Framework\Attributes\DataProvider;

final class UuidTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public static function validValues(): iterable
    {
        yield 'version 4' => ['f47ac10b-58cc-4372-a567-0e02b2c3d479'];
        yield 'version 7 uppercase' => ['018F3C1A-7B2C-7D3E-8F4A-5B6C7D8E9F0A'];
        yield 'nil' => ['00000000-0000-0000-0000-000000000000'];
    }

    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_a_valid_value(mixed $value): void
    {
        self::assertSame([], new Uuid()->validate(context: new ValidationContext([]), value: $value));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidValues(): iterable
    {
        yield 'no hyphens' => ['f47ac10b58cc4372a5670e02b2c3d479'];
        yield 'braces' => ['{f47ac10b-58cc-4372-a567-0e02b2c3d479}'];
        yield 'too short' => ['f47ac10b-58cc-4372-a567-0e02b2c3d47'];
        yield 'not hex' => ['g47ac10b-58cc-4372-a567-0e02b2c3d479'];
        yield 'trailing newline' => ["f47ac10b-58cc-4372-a567-0e02b2c3d479\n"];
        yield 'integer' => [42];
        yield 'null' => [null];
    }

    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_an_invalid_value(mixed $value): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be a valid UUID')],
            new Uuid()->validate(context: new ValidationContext([]), value: $value),
        );
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} must be a valid UUID', new Uuid()->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new Uuid(message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: '{input} is wrong')],
            $rule->validate(context: new ValidationContext([]), value: 'f47ac10b58cc4372a5670e02b2c3d479'),
        );
    }
}
