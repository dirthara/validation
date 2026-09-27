<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Validator;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\Rule\Text\Email;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidatorFactory;
use Dirthara\Validation\Rule\Structure\Nested;
use Dirthara\Validation\Rule\Presence\Required;
use Dirthara\Validation\Exception\InvalidRuleException;

final class ValidatorFactoryTest extends TestCase
{
    #[Test]
    public function it_creates_a_validator_from_rules_per_field(): void
    {
        $validator = new ValidatorFactory()->create([
            'name' => new Required(),
            'email' => [new Required(), new Email()],
            'address' => new Nested(new ValidatorFactory()->create(['email' => new Email()])),
        ]);

        self::assertInstanceOf(Validator::class, $validator);
        self::assertEquals(
            [
                new ValidationError(messageKey: '{input} is required', path: ['name']),
                new ValidationError(messageKey: '{input} must be a valid email address', path: ['email']),
                new ValidationError(messageKey: '{input} must be a valid email address', path: ['address', 'email']),
            ],
            $validator->validate(['email' => 'invalid', 'address' => ['email' => 'invalid']])->errors,
        );
    }

    #[Test]
    public function it_creates_a_validator_for_literal_string_field_names(): void
    {
        $errors = new ValidatorFactory()->create([
            'contact.name' => new Required(),
            'contact.email' => new Email(),
        ])->validate([
            'contact.email' => 'invalid',
        ])->errors;

        self::assertSame(['contact.name'], $errors[0]->path);
        self::assertSame('contact.name is required', $errors[0]->message);
        self::assertSame(['contact.email'], $errors[1]->path);
        self::assertSame('contact.email must be a valid email address', $errors[1]->message);
    }

    #[Test]
    public function it_rejects_a_field_rule_that_is_not_a_rule(): void
    {
        try {
            // @mago-expect analysis:possibly-invalid-argument The invalid rule is the point of the test
            new ValidatorFactory()->create(['name' => new Email(), 'email' => [new Email(), 'required']]);
            self::fail('Expected an InvalidRuleException.');
        } catch (InvalidRuleException $exception) {
            self::assertSame('Rule "string" is not a valid rule.', $exception->getMessage());
            self::assertSame(['rule' => 'required', 'field' => 'email'], $exception->context);
        }
    }

    #[Test]
    public function it_rejects_a_numeric_field_name_during_construction(): void
    {
        try {
            // @mago-expect analysis:possibly-invalid-argument Numeric validator field names are invalid configuration
            new ValidatorFactory()->create([0 => new Required()]);
            self::fail('Expected invalid rule configuration.');
        } catch (InvalidRuleException $exception) {
            self::assertSame('Validator field names must be strings.', $exception->getMessage());
            self::assertSame(['field' => 0], $exception->context);
        }
    }

    #[Test]
    public function it_rejects_a_numeric_string_key_converted_to_integer_by_php(): void
    {
        $this->expectException(InvalidRuleException::class);

        // @mago-expect analysis:possibly-invalid-argument PHP converts this numeric string key to an integer
        new ValidatorFactory()->create(['1' => new Required()]);
    }
}
