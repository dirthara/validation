<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\RuleSet;
use Dirthara\Validation\Rule\Each;
use Dirthara\Validation\Validator;
use Dirthara\Validation\Rule\Email;
use Dirthara\Validation\Rule\Nested;
use Dirthara\Validation\Rule\Present;
use Dirthara\Validation\Rule\Nullable;
use Dirthara\Validation\Rule\Required;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidatorFactory;
use Dirthara\Validation\Contract\Validator as ValidatorContract;

final class ValidatorTest extends TestCase
{
    #[Test]
    public function it_passes_input_that_meets_every_rule(): void
    {
        $validator = new Validator(['email' => RuleSet::from([new Required(), new Email()])]);

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
                new ValidationError(
                    message: 'The value must be a valid email address.',
                    code: 'email',
                    path: ['email'],
                ),
                new ValidationError(
                    message: 'The value must be a valid email address.',
                    code: 'email',
                    path: ['backup'],
                ),
            ],
            $validator->validate(['email' => 'invalid', 'backup' => 'also invalid'])->errors,
        );
    }

    #[Test]
    public function it_tells_a_missing_field_from_a_null_one(): void
    {
        $validator = new ValidatorFactory()->create([
            'name' => new Required(),
            'deleted_at' => [new Present(), new Nullable(), new Email()],
            'nickname' => new Email(),
        ]);

        self::assertEquals(
            [
                new ValidationError(message: 'The value is required.', code: 'required', path: ['name']),
                new ValidationError(message: 'The value must be present.', code: 'present', path: ['deleted_at']),
            ],
            $validator->validate([])->errors,
        );
        self::assertEquals(
            [
                new ValidationError(message: 'The value is required.', code: 'required', path: ['name']),
                new ValidationError(
                    message: 'The value must be a valid email address.',
                    code: 'email',
                    path: ['nickname'],
                ),
            ],
            $validator->validate(['name' => null, 'deleted_at' => null, 'nickname' => null])->errors,
        );
    }

    #[Test]
    public function it_accepts_input_with_integer_keys(): void
    {
        $validator = new ValidatorFactory()->create(['email' => new Email()]);

        self::assertSame([], $validator->validate([0 => 'first', 'email' => 'a@example.com'])->errors);
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
                message: 'The value must be a valid email address.',
                code: 'email',
                path: ['contacts', 1, 'email'],
            )],
            $validator->validate(['contacts' => [['email' => 'a@example.com'], ['email' => 'invalid']]])->errors,
        );
    }
}
