<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Structure;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\Rule\Text\Email;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidatorFactory;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Rule\Structure\Nested;

final class NestedTest extends TestCase
{
    #[Test]
    public function it_returns_the_errors_of_the_nested_validator(): void
    {
        $rule = new Nested(new ValidatorFactory()->create(['email' => new Email()]));

        self::assertSame([], $rule->validate(context: new ValidationContext([]), value: ['email' => 'a@example.com']));
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be a valid email address', path: ['email'])],
            $rule->validate(context: new ValidationContext([]), value: ['email' => 'invalid']),
        );
    }

    #[Test]
    public function it_rejects_a_value_that_is_not_an_array(): void
    {
        $rule = new Nested(new ValidatorFactory()->create(['email' => new Email()]));

        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be an array')],
            $rule->validate(context: new ValidationContext([]), value: 'a@example.com'),
        );
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} must be an array', new Nested(new ValidatorFactory()->create([]))->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new Nested(new ValidatorFactory()->create([]), message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: '{input} is wrong')],
            $rule->validate(context: new ValidationContext([]), value: 'invalid'),
        );
    }
}
