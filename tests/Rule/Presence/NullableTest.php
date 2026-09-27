<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Presence;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Missing;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\Contract\AcceptsValue;
use Dirthara\Validation\Rule\Presence\Nullable;

final class NullableTest extends TestCase
{
    #[Test]
    public function it_accepts_null_and_nothing_else(): void
    {
        $rule = new Nullable();

        self::assertInstanceOf(AcceptsValue::class, $rule);
        self::assertTrue($rule->accepts(null));
        self::assertFalse($rule->accepts(''));
        self::assertFalse($rule->accepts(0));
        self::assertFalse($rule->accepts(Missing::Value));
    }

    #[Test]
    public function it_rejects_nothing_itself(): void
    {
        self::assertSame([], new Nullable()->validate('value'));
        self::assertSame([], new Nullable()->validate(null));
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} may be null', new Nullable()->message);
        self::assertSame('{input} is wrong', new Nullable(message: '{input} is wrong')->message);
    }
}
