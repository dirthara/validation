<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Presence;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Missing;
use Dirthara\Validation\Contract\Rule;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Rule\Presence\RequiredIf;

final class RequiredIfTest extends TestCase
{
    #[Test]
    public function it_is_a_rule_that_validates_a_missing_value(): void
    {
        $rule = new RequiredIf(field: 'account_type', value: 'business');

        self::assertInstanceOf(Rule::class, $rule);
        self::assertTrue($rule->validatesMissing);
    }

    /**
     * @return list<ValidationError>
     */
    private static function required(): array
    {
        return [
            new ValidationError(messageKey: '{input} is required when {other} is {value}', parameters: [
                'other' => 'account_type',
                'value' => 'business',
            ]),
        ];
    }

    #[Test]
    public function it_requires_a_value_when_the_other_field_equals_the_value(): void
    {
        $rule = new RequiredIf(field: 'account_type', value: 'business');
        $context = new ValidationContext(['account_type' => 'business']);

        self::assertEquals(self::required(), $rule->validate(Missing::Value, $context));
        self::assertEquals(self::required(), $rule->validate(null, $context));
        self::assertSame([], $rule->validate('Acme', $context));
        self::assertSame([], $rule->validate('', $context));
    }

    #[Test]
    public function it_requires_nothing_when_the_other_field_has_another_value(): void
    {
        $rule = new RequiredIf(field: 'account_type', value: 'business');

        self::assertSame([], $rule->validate(Missing::Value, new ValidationContext(['account_type' => 'personal'])));
        self::assertSame([], $rule->validate(null, new ValidationContext(['account_type' => null])));
    }

    #[Test]
    public function it_compares_the_other_field_strictly(): void
    {
        $rule = new RequiredIf(field: 'employees', value: 1);

        self::assertSame([], $rule->validate(Missing::Value, new ValidationContext(['employees' => '1'])));
        self::assertNotSame([], $rule->validate(Missing::Value, new ValidationContext(['employees' => 1])));
    }

    #[Test]
    public function it_requires_nothing_when_the_other_field_is_missing(): void
    {
        $rule = new RequiredIf(field: 'account_type', value: 'business');

        self::assertSame([], $rule->validate(Missing::Value, new ValidationContext([])));
        self::assertSame([], $rule->validate(null, new ValidationContext([])));
    }

    #[Test]
    public function it_can_require_a_value_when_the_other_field_is_null(): void
    {
        $rule = new RequiredIf(field: 'account_type', value: null);

        self::assertNotSame([], $rule->validate(Missing::Value, new ValidationContext(['account_type' => null])));
        self::assertSame([], $rule->validate(Missing::Value, new ValidationContext([])));
    }

    #[Test]
    public function it_has_a_default_message_and_takes_a_custom_one(): void
    {
        self::assertSame(
            '{input} is required when {other} is {value}',
            new RequiredIf('account_type', 'business')->message,
        );

        $rule = new RequiredIf('account_type', 'business', message: '{input} is needed for {value} accounts');

        self::assertSame(
            '{input} is needed for {value} accounts',
            $rule->validate(null, new ValidationContext(['account_type' => 'business']))[0]->messageKey,
        );
    }
}
