<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests;

use stdClass;
use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Missing;
use Dirthara\Validation\RuleSet;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidatorFactory;
use Dirthara\Validation\Rule\String\Email;
use Dirthara\Validation\Rule\Presence\Present;
use Dirthara\Validation\Rule\Structure\Nested;
use Dirthara\Validation\Rule\Presence\Nullable;
use Dirthara\Validation\Rule\Presence\Required;
use Dirthara\Validation\Tests\Fixtures\FailingRule;
use Dirthara\Validation\Exception\InvalidRuleException;

final class RuleSetTest extends TestCase
{
    #[Test]
    public function it_validates_with_a_single_rule(): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} failed')],
            RuleSet::from(new FailingRule())->validate('value'),
        );
    }

    #[Test]
    public function it_validates_with_a_single_nested_rule(): void
    {
        $rules = RuleSet::from(new Nested(new ValidatorFactory()->create(['email' => new Email()])));

        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be a valid email address', path: ['email'])],
            $rules->validate(['email' => 'invalid']),
        );
        self::assertEquals([new ValidationError(messageKey: '{input} must be an array')], $rules->validate('invalid'));
    }

    #[Test]
    public function it_passes_anything_without_rules(): void
    {
        self::assertSame([], RuleSet::from([])->validate('value'));
    }

    #[Test]
    public function it_rejects_an_entry_that_is_not_a_rule(): void
    {
        $entry = new stdClass();

        try {
            // @mago-expect analysis:possibly-invalid-argument The invalid entry is the point of the test
            RuleSet::from([new Email(), $entry]);
            self::fail('Expected an InvalidRuleException.');
        } catch (InvalidRuleException $exception) {
            self::assertSame('Rule "stdClass" is not a valid rule.', $exception->getMessage());
            self::assertSame(['rule' => $entry], $exception->context);
        }
    }

    #[Test]
    public function it_rejects_a_validator_that_is_not_wrapped_in_a_nested_rule(): void
    {
        $this->expectException(InvalidRuleException::class);

        // @mago-expect analysis:possibly-invalid-argument An unwrapped validator is the point of the test
        RuleSet::from([new ValidatorFactory()->create([])]);
    }

    #[Test]
    public function it_stops_at_the_first_rule_that_fails(): void
    {
        $rules = RuleSet::from([new Email(), new FailingRule('first'), new FailingRule('second')]);

        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be a valid email address')],
            $rules->validate('invalid'),
        );
        self::assertEquals(
            [new ValidationError(messageKey: '{input} failed', path: ['first'])],
            $rules->validate('a@example.com'),
        );
    }

    #[Test]
    public function it_skips_every_rule_for_a_missing_value(): void
    {
        self::assertSame([], RuleSet::from([new Email(), new FailingRule()])->validate(Missing::Value));
    }

    #[Test]
    public function it_passes_null_to_every_rule(): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be a valid email address')],
            RuleSet::from(new Email())->validate(null),
        );
    }

    #[Test]
    public function it_runs_only_the_rules_that_validate_a_missing_value_for_one(): void
    {
        $rules = RuleSet::from([new FailingRule(), new Required(), new Email()]);

        self::assertEquals([new ValidationError(messageKey: '{input} is required')], $rules->validate(Missing::Value));
        self::assertEquals([new ValidationError(messageKey: '{input} failed')], $rules->validate('a@example.com'));
    }

    #[Test]
    public function it_runs_every_rule_that_validates_a_missing_value_in_order(): void
    {
        $rules = RuleSet::from([new Email(), new Present(), new Required()]);

        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be present')],
            $rules->validate(Missing::Value),
        );
    }

    #[Test]
    public function it_passes_a_value_that_any_of_its_rules_accepts_wherever_that_rule_is(): void
    {
        self::assertSame([], RuleSet::from([new Email(), new FailingRule(), new Nullable()])->validate(null));
        self::assertSame([], RuleSet::from([new Required(), new Nullable(), new Email()])->validate(null));
    }

    #[Test]
    public function it_runs_its_rules_for_a_value_that_none_of_its_rules_accepts(): void
    {
        $rules = RuleSet::from([new Nullable(), new Email()]);

        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be a valid email address')],
            $rules->validate('invalid'),
        );
        self::assertSame([], $rules->validate(Missing::Value));
    }

    #[Test]
    public function it_runs_a_nested_rule_after_the_rules_before_it_pass(): void
    {
        $rules = RuleSet::from([
            new Required(),
            new Nested(new ValidatorFactory()->create(['email' => new Email()])),
        ]);

        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be a valid email address', path: ['email'])],
            $rules->validate(['email' => 'invalid']),
        );
    }
}
