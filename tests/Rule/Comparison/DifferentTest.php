<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Comparison;

use TypeError;
use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Contract\Rule;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Rule\Comparison\Different;

final class DifferentTest extends TestCase
{
    #[Test]
    public function it_is_a_rule_that_skips_a_missing_value(): void
    {
        self::assertInstanceOf(Rule::class, new Different('username'));
        self::assertFalse(new Different('username')->validatesMissing);
    }

    #[Test]
    public function it_accepts_a_value_different_from_the_other_field(): void
    {
        self::assertSame(
            [],
            new Different('username')->validate('secret', new ValidationContext(['username' => 'ada'])),
        );
    }

    #[Test]
    public function it_compares_strictly(): void
    {
        self::assertSame([], new Different('age')->validate('18', new ValidationContext(['age' => 18])));
        self::assertSame([], new Different('age')->validate(18.0, new ValidationContext(['age' => 18])));
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be different from {other}', parameters: ['other' => 'age'])],
            new Different('age')->validate(18, new ValidationContext(['age' => 18])),
        );
    }

    #[Test]
    public function it_rejects_any_value_when_the_other_field_is_missing(): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be different from {other}', parameters: [
                'other' => 'username',
            ])],
            new Different('username')->validate('secret', new ValidationContext([])),
        );
    }

    #[Test]
    public function it_compares_with_an_other_field_that_is_null(): void
    {
        $context = new ValidationContext(['username' => null]);

        self::assertSame([], new Different('username')->validate('secret', $context));
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be different from {other}', parameters: [
                'other' => 'username',
            ])],
            new Different('username')->validate(null, $context),
        );
    }

    #[Test]
    public function it_has_a_default_message_and_takes_a_custom_one(): void
    {
        self::assertSame('{input} must be different from {other}', new Different('username')->message);

        $rule = new Different('username', message: '{input} may not equal {other}');

        self::assertEquals(
            [new ValidationError(messageKey: '{input} may not equal {other}', parameters: ['other' => 'username'])],
            $rule->validate('ada', new ValidationContext(['username' => 'ada'])),
        );
    }

    #[Test]
    public function it_rejects_integer_field_references(): void
    {
        $this->expectException(TypeError::class);

        // @mago-expect analysis:invalid-argument Integer field references are outside the contract
        new Different(0);
    }
}
