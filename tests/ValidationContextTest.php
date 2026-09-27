<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests;

use TypeError;
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
        $context = new ValidationContext(['name' => 'Ada', 'nickname' => null]);

        self::assertTrue($context->has('name'));
        self::assertTrue($context->has('nickname'));
        self::assertFalse($context->has('email'));
    }

    #[Test]
    public function it_returns_the_value_of_a_field_or_missing(): void
    {
        $context = new ValidationContext(['name' => 'Ada', 'nickname' => null]);

        self::assertSame('Ada', $context->value('name'));
        self::assertNull($context->value('nickname'));
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

    #[Test]
    public function it_rejects_an_integer_field_for_has(): void
    {
        $context = new ValidationContext([]);
        $this->expectException(TypeError::class);

        // @mago-expect analysis:invalid-argument Integer field references are outside the contract
        $context->has(0);
    }

    #[Test]
    public function it_rejects_an_integer_field_for_value(): void
    {
        $context = new ValidationContext([]);
        $this->expectException(TypeError::class);

        // @mago-expect analysis:invalid-argument Integer field references are outside the contract
        $context->value(0);
    }
}
