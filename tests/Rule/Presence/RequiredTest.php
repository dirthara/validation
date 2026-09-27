<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Presence;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Missing;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Rule\Presence\Required;

final class RequiredTest extends TestCase
{
    #[Test]
    public function it_validates_a_missing_value(): void
    {
        self::assertTrue(new Required()->validatesMissing);
    }

    #[Test]
    public function it_accepts_any_value_but_null(): void
    {
        self::assertSame([], new Required()->validate(context: new ValidationContext([]), value: 'value'));
        self::assertSame([], new Required()->validate(context: new ValidationContext([]), value: ''));
        self::assertSame([], new Required()->validate(context: new ValidationContext([]), value: 0));
        self::assertSame([], new Required()->validate(context: new ValidationContext([]), value: false));
    }

    #[Test]
    public function it_rejects_a_missing_value_and_null(): void
    {
        $required = [new ValidationError(messageKey: '{input} is required')];

        self::assertEquals($required, new Required()->validate(
            context: new ValidationContext([]),
            value: Missing::Value,
        ));
        self::assertEquals($required, new Required()->validate(context: new ValidationContext([]), value: null));
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} is required', new Required()->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new Required(message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: '{input} is wrong')],
            $rule->validate(context: new ValidationContext([]), value: null),
        );
    }
}
