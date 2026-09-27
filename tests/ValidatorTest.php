<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Rule\Each;
use Dirthara\Validation\Validator;
use Dirthara\Validation\Rule\Email;
use Dirthara\Validation\Rule\Nested;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Internal\RuleSet;
use Dirthara\Validation\Tests\Fixtures\RequiredRule;
use Dirthara\Validation\Contract\Validator as ValidatorContract;

final class ValidatorTest extends TestCase
{
    #[Test]
    public function it_passes_input_that_meets_every_rule(): void
    {
        $validator = new Validator(['email' => RuleSet::from([new RequiredRule(), new Email()])]);

        self::assertInstanceOf(ValidatorContract::class, $validator);
        self::assertSame([], $validator->validate(['email' => 'a@example.com'])->errors);
    }

    #[Test]
    public function it_prefixes_each_error_with_its_field(): void
    {
        $validator = new Validator([
            'email' => RuleSet::from(new Email()),
            'backup' => RuleSet::from(new Email()),
        ]);

        self::assertEquals(
            [
                new ValidationError(field: 'email', message: 'The value must be a valid email address.', code: 'email'),
                new ValidationError(
                    field: 'backup',
                    message: 'The value must be a valid email address.',
                    code: 'email',
                ),
            ],
            $validator->validate(['email' => 'invalid', 'backup' => 'also invalid'])->errors,
        );
    }

    #[Test]
    public function it_tells_a_missing_field_from_a_null_one(): void
    {
        $validator = new Validator(['name' => RuleSet::from(new RequiredRule())]);
        $required = new ValidationError(field: 'name', message: 'The value is required.', code: 'required');

        self::assertEquals([$required], $validator->validate([])->errors);
        self::assertEquals([$required], $validator->validate(['name' => null])->errors);
        self::assertSame([], $validator->validate(['name' => 'Ada'])->errors);
    }

    #[Test]
    public function it_ignores_input_without_rules(): void
    {
        $validator = new Validator(['email' => RuleSet::from(new Email())]);

        self::assertSame([], $validator->validate(['email' => 'a@example.com', 'other' => 'anything'])->errors);
        self::assertSame([], new Validator([])->validate(['email' => 'invalid'])->errors);
    }

    #[Test]
    public function it_nests_the_field_names_of_nested_rules(): void
    {
        $address = new Validator(['email' => RuleSet::from(new Email())]);
        $validator = new Validator(['contacts' => RuleSet::from(new Each(new Nested($address)))]);

        self::assertEquals(
            [new ValidationError(
                field: 'contacts.1.email',
                message: 'The value must be a valid email address.',
                code: 'email',
            )],
            $validator->validate(['contacts' => [['email' => 'a@example.com'], ['email' => 'invalid']]])->errors,
        );
    }
}
