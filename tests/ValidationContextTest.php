<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Missing;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationContext;

final class ValidationContextTest extends TestCase
{
    #[Test]
    public function it_carries_its_input(): void
    {
        self::assertSame(['a' => 1], new ValidationContext(['a' => 1])->input);
    }

    #[Test]
    public function it_has_a_field_that_is_present_even_when_it_is_null(): void
    {
        $context = new ValidationContext(['name' => 'Ada', 'nickname' => null, 0 => 'first']);

        self::assertTrue($context->has('name'));
        self::assertTrue($context->has('nickname'));
        self::assertTrue($context->has(0));
        self::assertFalse($context->has('email'));
    }

    #[Test]
    public function it_returns_the_value_of_a_field_or_missing(): void
    {
        $context = new ValidationContext(['name' => 'Ada', 'nickname' => null, 0 => 'first']);

        self::assertSame('Ada', $context->value('name'));
        self::assertNull($context->value('nickname'));
        self::assertSame('first', $context->value(0));
        self::assertSame(Missing::Value, $context->value('email'));
    }

    #[Test]
    public function it_treats_a_field_name_with_a_dot_as_a_literal_key(): void
    {
        $context = new ValidationContext(['address' => ['street' => 'Main Street'], 'a.b' => 'literal']);

        self::assertFalse($context->has('address.street'));
        self::assertSame(Missing::Value, $context->value('address.street'));
        self::assertSame('literal', $context->value('a.b'));
    }
}
