<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests;

use stdClass;
use PHPUnit\Framework\TestCase;
use Dirthara\Validation\RuleSet;
use Dirthara\Validation\Rule\Email;
use Dirthara\Validation\Rule\Nested;
use Dirthara\Validation\Rule\Required;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidatorFactory;
use Dirthara\Validation\Tests\Fixtures\FailingRule;
use Dirthara\Validation\Exception\InvalidRuleException;

final class RuleSetTest extends TestCase
{
    #[Test]
    public function it_validates_with_a_single_rule(): void
    {
        self::assertEquals(
            [new ValidationError(message: 'The value failed.', code: 'failing')],
            RuleSet::from(new FailingRule())->validate('value'),
        );
    }

    #[Test]
    public function it_validates_with_a_single_nested_rule(): void
    {
        $rules = RuleSet::from(new Nested(new ValidatorFactory()->create(['email' => new Email()])));

        self::assertEquals(
            [new ValidationError(message: 'The value must be a valid email address.', code: 'email', path: ['email'])],
            $rules->validate(['email' => 'invalid']),
        );
        self::assertEquals(
            [new ValidationError(message: 'The value must be an array.', code: 'array')],
            $rules->validate('invalid'),
        );
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
            [new ValidationError(message: 'The value must be a valid email address.', code: 'email')],
            $rules->validate('invalid'),
        );
        self::assertEquals(
            [new ValidationError(message: 'The value failed.', code: 'failing', path: ['first'])],
            $rules->validate('a@example.com'),
        );
    }

    #[Test]
    public function it_skips_every_rule_for_a_null_value(): void
    {
        self::assertSame([], RuleSet::from([new Email(), new FailingRule()])->validate(null));
    }

    #[Test]
    public function it_runs_only_the_required_rule_for_a_null_value(): void
    {
        $rules = RuleSet::from([new FailingRule(), new Required(), new Email()]);

        self::assertEquals(
            [new ValidationError(message: 'The value is required.', code: 'required')],
            $rules->validate(null),
        );
        self::assertEquals(
            [new ValidationError(message: 'The value failed.', code: 'failing')],
            $rules->validate('a@example.com'),
        );
    }

    #[Test]
    public function it_runs_a_nested_rule_after_the_rules_before_it_pass(): void
    {
        $rules = RuleSet::from([
            new Required(),
            new Nested(new ValidatorFactory()->create(['email' => new Email()])),
        ]);

        self::assertEquals(
            [new ValidationError(message: 'The value must be a valid email address.', code: 'email', path: ['email'])],
            $rules->validate(['email' => 'invalid']),
        );
    }
}
