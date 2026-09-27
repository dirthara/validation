<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Comparison;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Contract\Rule;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Rule\Comparison\Same;

final class SameTest extends TestCase
{
    #[Test]
    public function it_is_a_rule_that_skips_a_missing_value(): void
    {
        self::assertInstanceOf(Rule::class, new Same('email'));
        self::assertFalse(new Same('email')->validatesMissing);
    }

    #[Test]
    public function it_accepts_a_value_identical_to_the_other_field(): void
    {
        self::assertSame([], new Same('email')->validate('secret', new ValidationContext(['email' => 'secret'])));
        self::assertSame([], new Same(0)->validate(18, new ValidationContext([0 => 18])));
    }

    #[Test]
    public function it_compares_strictly(): void
    {
        $error = [new ValidationError(messageKey: '{input} must be the same as {other}', parameters: [
            'other' => 'age',
        ])];

        self::assertEquals($error, new Same('age')->validate('18', new ValidationContext(['age' => 18])));
        self::assertEquals($error, new Same('age')->validate(18.0, new ValidationContext(['age' => 18])));
    }

    #[Test]
    public function it_rejects_any_value_when_the_other_field_is_missing(): void
    {
        $error = [new ValidationError(messageKey: '{input} must be the same as {other}', parameters: [
            'other' => 'email',
        ])];

        self::assertEquals($error, new Same('email')->validate('secret', new ValidationContext([])));
        self::assertEquals($error, new Same('email')->validate(null, new ValidationContext([])));
    }

    #[Test]
    public function it_compares_with_an_other_field_that_is_null(): void
    {
        $context = new ValidationContext(['email' => null]);

        self::assertSame([], new Same('email')->validate(null, $context));
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be the same as {other}', parameters: [
                'other' => 'email',
            ])],
            new Same('email')->validate('secret', $context),
        );
    }

    #[Test]
    public function it_has_a_default_message_and_takes_a_custom_one(): void
    {
        self::assertSame('{input} must be the same as {other}', new Same('email')->message);

        $rule = new Same('email', message: '{input} does not match {other}');

        self::assertEquals(
            [new ValidationError(messageKey: '{input} does not match {other}', parameters: ['other' => 'email'])],
            $rule->validate('other', new ValidationContext(['email' => 'secret'])),
        );
    }
}
