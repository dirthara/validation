<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\RuleSet;
use Dirthara\Validation\Validator;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\Rule\Text\Email;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidatorFactory;
use Dirthara\Validation\Rule\Collection\Each;
use Dirthara\Validation\Rule\Presence\Present;
use Dirthara\Validation\Rule\Structure\Nested;
use Dirthara\Validation\Rule\Presence\Nullable;
use Dirthara\Validation\Rule\Presence\Required;
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
                new ValidationError(messageKey: '{input} must be a valid email address', path: ['email']),
                new ValidationError(messageKey: '{input} must be a valid email address', path: ['backup']),
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
                new ValidationError(messageKey: '{input} is required', path: ['name']),
                new ValidationError(messageKey: '{input} must be present', path: ['deleted_at']),
            ],
            $validator->validate([])->errors,
        );
        self::assertEquals(
            [
                new ValidationError(messageKey: '{input} is required', path: ['name']),
                new ValidationError(messageKey: '{input} must be a valid email address', path: ['nickname']),
            ],
            $validator->validate(['name' => null, 'deleted_at' => null, 'nickname' => null])->errors,
        );
    }

    #[Test]
    public function it_accepts_collections_inside_named_fields(): void
    {
        $validator = new ValidatorFactory()->create(['emails' => new Each(new Email())]);

        self::assertSame([], $validator->validate(['emails' => ['a@example.com', 'b@example.com']])->errors);
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
            [new ValidationError(messageKey: '{input} must be a valid email address', path: ['contacts', 1, 'email'])],
            $validator->validate(['contacts' => [['email' => 'a@example.com'], ['email' => 'invalid']]])->errors,
        );
    }

    #[Test]
    public function it_renders_messages_with_the_path_of_the_field(): void
    {
        $validator = new ValidatorFactory()->create([
            'email' => [new Required(), new Email()],
            'contacts' => new Each(new Nested(new ValidatorFactory()->create(['email' => new Email()]))),
        ]);

        $errors = $validator->validate(['contacts' => [['email' => 'invalid']]])->errors;

        self::assertSame('{input} is required', $errors[0]->messageKey);
        self::assertSame('email is required', $errors[0]->message);
        self::assertSame('{input} must be a valid email address', $errors[1]->messageKey);
        self::assertSame('contacts.0.email must be a valid email address', $errors[1]->message);
    }
}
