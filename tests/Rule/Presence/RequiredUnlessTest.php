<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Presence;

use TypeError;
use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Missing;
use Dirthara\Validation\Contract\Rule;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Rule\Presence\RequiredUnless;

final class RequiredUnlessTest extends TestCase
{
    #[Test]
    public function it_is_a_rule_that_validates_a_missing_value(): void
    {
        $rule = new RequiredUnless(field: 'account_type', value: 'personal');

        self::assertInstanceOf(Rule::class, $rule);
        self::assertTrue($rule->validatesMissing);
    }

    /**
     * @return list<ValidationError>
     */
    private static function required(): array
    {
        return [
            new ValidationError(messageKey: '{input} is required unless {other} is {value}', parameters: [
                'other' => 'account_type',
                'value' => 'personal',
            ]),
        ];
    }

    #[Test]
    public function it_requires_nothing_when_the_other_field_equals_the_value(): void
    {
        $rule = new RequiredUnless(field: 'account_type', value: 'personal');
        $context = new ValidationContext(['account_type' => 'personal']);

        self::assertSame([], $rule->validate(Missing::Value, $context));
        self::assertSame([], $rule->validate(null, $context));
    }

    #[Test]
    public function it_requires_a_value_when_the_other_field_has_another_value(): void
    {
        $rule = new RequiredUnless(field: 'account_type', value: 'personal');
        $context = new ValidationContext(['account_type' => 'business']);

        self::assertEquals(self::required(), $rule->validate(Missing::Value, $context));
        self::assertEquals(self::required(), $rule->validate(null, $context));
        self::assertSame([], $rule->validate('Acme', $context));
    }

    #[Test]
    public function it_compares_the_other_field_strictly(): void
    {
        $rule = new RequiredUnless(field: 'employees', value: 1);

        self::assertNotSame([], $rule->validate(Missing::Value, new ValidationContext(['employees' => '1'])));
        self::assertSame([], $rule->validate(Missing::Value, new ValidationContext(['employees' => 1])));
    }

    #[Test]
    public function it_requires_a_value_when_the_other_field_is_missing(): void
    {
        $rule = new RequiredUnless(field: 'account_type', value: 'personal');

        self::assertEquals(self::required(), $rule->validate(Missing::Value, new ValidationContext([])));
        self::assertEquals(self::required(), $rule->validate(null, new ValidationContext([])));
        self::assertSame([], $rule->validate('Acme', new ValidationContext([])));
    }

    #[Test]
    public function it_requires_a_value_when_the_other_field_is_null(): void
    {
        $rule = new RequiredUnless(field: 'account_type', value: 'personal');

        self::assertEquals(self::required(), $rule->validate(null, new ValidationContext(['account_type' => null])));
    }

    #[Test]
    public function it_has_a_default_message_and_takes_a_custom_one(): void
    {
        self::assertSame(
            '{input} is required unless {other} is {value}',
            new RequiredUnless('account_type', 'personal')->message,
        );

        $rule = new RequiredUnless(
            'account_type',
            'personal',
            message: '{input} is needed for all but {value} accounts',
        );

        self::assertSame(
            '{input} is needed for all but {value} accounts',
            $rule->validate(null, new ValidationContext([]))[0]->messageKey,
        );
    }

    #[Test]
    public function it_rejects_integer_field_references(): void
    {
        $this->expectException(TypeError::class);

        // @mago-expect analysis:invalid-argument Integer field references are outside the contract
        new RequiredUnless(0, 1);
    }

    #[Test]
    public function it_never_matches_an_absent_reference_to_the_configured_sentinel(): void
    {
        $rule = new RequiredUnless('type', Missing::Value);

        self::assertCount(1, $rule->validate(Missing::Value, new ValidationContext([])));
        self::assertCount(1, $rule->validate(null, new ValidationContext([])));
        self::assertSame([], $rule->validate('supplied', new ValidationContext([])));
        self::assertCount(0, $rule->validate(null, new ValidationContext(['type' => Missing::Value])));
    }
}
