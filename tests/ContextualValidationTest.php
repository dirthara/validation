<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests;

use PHPUnit\Framework\TestCase;

use function array_map;

use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\Rule\Text\Email;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidatorFactory;
use Dirthara\Validation\Rule\Text\MinLength;
use Dirthara\Validation\Rule\Collection\Each;
use Dirthara\Validation\Rule\Comparison\Same;
use Dirthara\Validation\Rule\Type\StringType;
use Dirthara\Validation\Rule\Structure\Nested;
use Dirthara\Validation\Rule\Presence\Nullable;
use Dirthara\Validation\Rule\Presence\Required;
use Dirthara\Validation\Rule\Presence\RequiredIf;
use Dirthara\Validation\Rule\Comparison\Different;
use Dirthara\Validation\Rule\Presence\RequiredUnless;

final class ContextualValidationTest extends TestCase
{
    #[Test]
    public function it_compares_a_field_with_another_field_of_the_same_input(): void
    {
        $validator = new ValidatorFactory()->create([
            'email' => [new Required(), new StringType()],
            'email_confirmation' => [new Required(), new Same('email')],
        ]);

        self::assertSame([], $validator->validate(['email' => 'secret', 'email_confirmation' => 'secret'])->errors);

        $errors = $validator->validate(['email' => 'secret', 'email_confirmation' => 'other'])->errors;

        self::assertSame(['email_confirmation'], $errors[0]->path);
        self::assertSame('{input} must be the same as {other}', $errors[0]->messageKey);
        self::assertSame(['other' => 'email'], $errors[0]->parameters);
        self::assertSame('email_confirmation must be the same as email', $errors[0]->message);
    }

    #[Test]
    public function it_skips_a_contextual_rule_for_a_missing_field_and_runs_it_for_null(): void
    {
        $validator = new ValidatorFactory()->create(['confirmation' => new Same('email')]);

        self::assertSame([], $validator->validate(['email' => 'secret'])->errors);
        self::assertSame(
            'confirmation must be the same as email',
            $validator->validate(['email' => 'secret', 'confirmation' => null])->errors[0]->message,
        );
    }

    #[Test]
    public function it_requires_a_field_depending_on_another_field(): void
    {
        $validator = new ValidatorFactory()->create([
            'company_name' => [new RequiredIf(field: 'account_type', value: 'business'), new StringType()],
            'date_of_birth' => [new RequiredUnless(field: 'account_type', value: 'business'), new StringType()],
        ]);

        $errors = $validator->validate(['account_type' => 'business'])->errors;

        self::assertCount(1, $errors);
        self::assertSame('company_name is required when account_type is business', $errors[0]->message);
        self::assertSame(['other' => 'account_type', 'value' => 'business'], $errors[0]->parameters);

        $errors = $validator->validate(['account_type' => 'personal'])->errors;

        self::assertCount(1, $errors);
        self::assertSame('date_of_birth is required unless account_type is business', $errors[0]->message);

        self::assertSame(
            'date_of_birth is required unless account_type is business',
            $validator->validate([])->errors[0]->message,
        );
    }

    #[Test]
    public function it_compares_with_the_siblings_inside_a_nested_validator(): void
    {
        $factory = new ValidatorFactory();
        $validator = $factory->create([
            'email' => new StringType(),
            'account' => new Nested($factory->create([
                'email' => new StringType(),
                'confirmation' => new Same('email'),
            ])),
        ]);

        self::assertSame(
            [],
            $validator->validate([
                'email' => 'outer',
                'account' => ['email' => 'inner', 'confirmation' => 'inner'],
            ])->errors,
        );

        $errors = $validator->validate([
            'email' => 'outer',
            'account' => ['email' => 'inner', 'confirmation' => 'outer'],
        ])->errors;

        self::assertSame(['account', 'confirmation'], $errors[0]->path);
        self::assertSame('account.confirmation must be the same as email', $errors[0]->message);
    }

    #[Test]
    public function it_passes_the_context_to_the_rules_of_each_item(): void
    {
        $validator = new ValidatorFactory()->create([
            'aliases' => new Each([new StringType(), new Different('username')]),
        ]);

        $errors = $validator->validate(['username' => 'ada', 'aliases' => ['lovelace', 'ada']])->errors;

        self::assertEquals(
            [new ValidationError(
                messageKey: '{input} must be different from {other}',
                parameters: ['other' => 'username'],
                path: ['aliases', 1],
            )],
            $errors,
        );
    }

    #[Test]
    public function it_runs_contextual_and_ordinary_rules_in_order_and_stops_at_the_first_failure(): void
    {
        $validator = new ValidatorFactory()->create([
            'email_confirmation' => [new StringType(), new Same('email'), new MinLength(5), new Email()],
        ]);

        self::assertSame(
            ['{input} must be a string'],
            array_map(
                static fn(ValidationError $error): string => $error->messageKey,
                $validator->validate(['email' => 'a@example.com', 'email_confirmation' => 42])->errors,
            ),
        );
        self::assertSame(
            ['{input} must be the same as {other}'],
            array_map(
                static fn(ValidationError $error): string => $error->messageKey,
                $validator->validate(['email' => 'a@example.com', 'email_confirmation' => 'abc'])->errors,
            ),
        );
        self::assertSame(
            ['{input} must be a valid email address'],
            array_map(
                static fn(ValidationError $error): string => $error->messageKey,
                $validator->validate(['email' => 'not-an-email', 'email_confirmation' => 'not-an-email'])->errors,
            ),
        );
    }

    #[Test]
    public function it_lets_nullable_accept_null_before_a_contextual_rule_runs(): void
    {
        $validator = new ValidatorFactory()->create([
            'company_name' => [new Nullable(), new RequiredIf(field: 'account_type', value: 'business')],
        ]);

        self::assertSame([], $validator->validate(['account_type' => 'business', 'company_name' => null])->errors);
        self::assertSame(
            'company_name is required when account_type is business',
            $validator->validate(['account_type' => 'business'])->errors[0]->message,
        );
    }
}
