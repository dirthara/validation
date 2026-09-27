<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Rule\Email;
use Dirthara\Validation\Rule\Nested;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidatorFactory;
use Dirthara\Validation\Tests\Fixtures\RequiredRule;

final class NestedTest extends TestCase
{
    #[Test]
    public function it_returns_the_errors_of_the_nested_validator(): void
    {
        $rule = new Nested(new ValidatorFactory()->create(['email' => new Email()]));

        self::assertSame([], $rule->validate(['email' => 'a@example.com']));
        self::assertEquals(
            [new ValidationError(field: 'email', message: 'The value must be a valid email address.', code: 'email')],
            $rule->validate(['email' => 'invalid']),
        );
    }

    #[Test]
    public function it_skips_a_missing_or_null_value(): void
    {
        $rule = new Nested(new ValidatorFactory()->create(['email' => new RequiredRule()]));

        self::assertSame([], $rule->validate('ignored', present: false));
        self::assertSame([], $rule->validate(null));
    }

    #[Test]
    public function it_rejects_a_value_that_is_not_an_array(): void
    {
        $rule = new Nested(new ValidatorFactory()->create(['email' => new Email()]));

        self::assertEquals(
            [new ValidationError(field: '', message: 'The value must be an array.', code: 'array')],
            $rule->validate('a@example.com'),
        );
    }
}
