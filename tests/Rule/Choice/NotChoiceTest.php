<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Choice;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Rule\Choice\NotChoice;
use PHPUnit\Framework\Attributes\DataProvider;

final class NotChoiceTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public static function validValues(): iterable
    {
        yield 'other' => ['archived'];
        yield 'numeric string' => ['1'];
        yield 'null' => [null];
    }

    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_a_valid_value(mixed $value): void
    {
        self::assertSame(
            [],
            new NotChoice(['draft', 'published', 1])->validate(context: new ValidationContext([]), value: $value),
        );
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidValues(): iterable
    {
        yield 'first' => ['draft'];
        yield 'second' => ['published'];
        yield 'integer' => [1];
    }

    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_an_invalid_value(mixed $value): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must not be one of {choices}', parameters: ['choices' => [
                'draft',
                'published',
                1,
            ]])],
            new NotChoice(['draft', 'published', 1])->validate(context: new ValidationContext([]), value: $value),
        );
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} must not be one of {choices}', new NotChoice(['draft', 'published', 1])->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new NotChoice(['draft', 'published', 1], message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: '{input} is wrong', parameters: ['choices' => ['draft', 'published', 1]])],
            $rule->validate(context: new ValidationContext([]), value: 'draft'),
        );
    }
}
