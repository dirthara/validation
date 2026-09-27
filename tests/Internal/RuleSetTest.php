<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Internal;

use stdClass;
use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Rule\Email;
use Dirthara\Validation\Rule\Nested;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Internal\RuleSet;
use Dirthara\Validation\ValidatorFactory;
use Dirthara\Validation\Tests\Fixtures\FailingRule;
use Dirthara\Validation\Tests\Fixtures\RequiredRule;
use Dirthara\Validation\Exception\InvalidRuleException;

final class RuleSetTest extends TestCase
{
    #[Test]
    public function it_validates_with_a_single_rule(): void
    {
        self::assertEquals(
            [new ValidationError(field: '', message: 'The value failed.', code: 'failing')],
            RuleSet::from(new FailingRule())->validate('value'),
        );
    }

    #[Test]
    public function it_validates_with_a_single_nested_rule(): void
    {
        $rules = RuleSet::from(new Nested(new ValidatorFactory()->create(['email' => new Email()])));

        self::assertEquals(
            [new ValidationError(field: 'email', message: 'The value must be a valid email address.', code: 'email')],
            $rules->validate(['email' => 'invalid']),
        );
        self::assertEquals(
            [new ValidationError(field: '', message: 'The value must be an array.', code: 'array')],
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
            // @mago-expect analysis:possibly-invalid-argument -- the invalid entry is the point of the test
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
    public function it_collects_the_errors_of_every_rule_in_order(): void
    {
        $errors = RuleSet::from([new FailingRule('first'), new Email(), new FailingRule()])->validate('invalid');

        self::assertEquals(
            [
                new ValidationError(field: 'first', message: 'The value failed.', code: 'failing'),
                new ValidationError(field: '', message: 'The value must be a valid email address.', code: 'email'),
                new ValidationError(field: '', message: 'The value failed.', code: 'failing'),
            ],
            $errors,
        );
    }

    #[Test]
    public function it_passes_presence_to_its_rules(): void
    {
        $rules = RuleSet::from(new RequiredRule());

        self::assertSame([], $rules->validate('value'));
        self::assertEquals(
            [new ValidationError(field: '', message: 'The value is required.', code: 'required')],
            $rules->validate('value', present: false),
        );
    }

    #[Test]
    public function it_runs_a_nested_rule_in_order_with_the_others(): void
    {
        $rules = RuleSet::from([
            new FailingRule(),
            new Nested(new ValidatorFactory()->create(['email' => new Email()])),
        ]);

        self::assertEquals(
            [
                new ValidationError(field: '', message: 'The value failed.', code: 'failing'),
                new ValidationError(field: 'email', message: 'The value must be a valid email address.', code: 'email'),
            ],
            $rules->validate(['email' => 'invalid']),
        );
    }
}
