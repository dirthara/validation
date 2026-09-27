<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Presence;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Missing;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Rule\Presence\Present;

final class PresentTest extends TestCase
{
    #[Test]
    public function it_validates_a_missing_value(): void
    {
        self::assertTrue(new Present()->validatesMissing);
    }

    #[Test]
    public function it_accepts_any_value_that_is_present_including_null(): void
    {
        self::assertSame([], new Present()->validate(context: new ValidationContext([]), value: 'value'));
        self::assertSame([], new Present()->validate(context: new ValidationContext([]), value: null));
        self::assertSame([], new Present()->validate(context: new ValidationContext([]), value: ''));
    }

    #[Test]
    public function it_rejects_a_missing_value(): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be present')],
            new Present()->validate(context: new ValidationContext([]), value: Missing::Value),
        );
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} must be present', new Present()->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new Present(message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: '{input} is wrong')],
            $rule->validate(context: new ValidationContext([]), value: Missing::Value),
        );
    }
}
