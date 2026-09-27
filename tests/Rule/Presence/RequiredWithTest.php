<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Presence;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Missing;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidatorFactory;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Rule\Collection\Each;
use Dirthara\Validation\Rule\Type\StringType;
use Dirthara\Validation\Rule\Structure\Nested;
use PHPUnit\Framework\Attributes\DataProvider;
use Dirthara\Validation\Rule\Presence\RequiredWith;

final class RequiredWithTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed, array<array-key, mixed>}>
     */
    public static function validValues(): iterable
    {
        yield 'missing reference and missing' => [Missing::Value, []];
        yield 'missing reference and null' => [null, []];
        yield 'missing reference and value' => ['supplied', []];
        yield 'null reference and missing' => [Missing::Value, ['other' => null]];
        yield 'null reference and null' => [null, ['other' => null]];
        yield 'null reference and value' => ['supplied', ['other' => null]];
        yield 'one reference and false' => [false, ['other' => 1]];
        yield 'one reference and zero' => [0, ['other' => 1]];
        yield 'one reference and empty string' => ['', ['other' => 1]];
        yield 'one reference and empty array' => [[], ['other' => 1]];
        yield 'one reference and value' => ['supplied', ['other' => 1]];
        yield 'string reference and value' => ['supplied', ['other' => '1']];
        yield 'false reference and value' => ['supplied', ['other' => false]];
        yield 'zero reference and value' => ['supplied', ['other' => 0]];
        yield 'empty string reference and value' => ['supplied', ['other' => '']];
        yield 'empty array reference and false' => [false, ['other' => []]];
        yield 'empty array reference and zero' => [0, ['other' => []]];
        yield 'empty array reference and empty string' => ['', ['other' => []]];
        yield 'empty array reference and empty array' => [[], ['other' => []]];
        yield 'empty array reference and value' => ['supplied', ['other' => []]];
    }

    /**
     * @param array<array-key, mixed> $input
     */
    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_valid_values(mixed $value, array $input): void
    {
        $rule = new RequiredWith('other');

        self::assertSame([], $rule->validate($value, new ValidationContext($input)));
    }

    /**
     * @return iterable<string, array{mixed, array<array-key, mixed>}>
     */
    public static function invalidValues(): iterable
    {
        yield 'one reference and missing' => [Missing::Value, ['other' => 1]];
        yield 'one reference and null' => [null, ['other' => 1]];
        yield 'string reference and missing' => [Missing::Value, ['other' => '1']];
        yield 'string reference and null' => [null, ['other' => '1']];
        yield 'false reference and missing' => [Missing::Value, ['other' => false]];
        yield 'false reference and null' => [null, ['other' => false]];
        yield 'zero reference and missing' => [Missing::Value, ['other' => 0]];
        yield 'zero reference and null' => [null, ['other' => 0]];
        yield 'empty string reference and missing' => [Missing::Value, ['other' => '']];
        yield 'empty string reference and null' => [null, ['other' => '']];
        yield 'empty array reference and missing' => [Missing::Value, ['other' => []]];
        yield 'empty array reference and null' => [null, ['other' => []]];
    }

    /**
     * @param array<array-key, mixed> $input
     */
    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_invalid_values(mixed $value, array $input): void
    {
        $rule = new RequiredWith('other');

        self::assertEquals(
            [
                new ValidationError(messageKey: '{input} is required when {other} is present', parameters: [
                    'other' => 'other',
                ]),
            ],
            $rule->validate($value, new ValidationContext($input)),
        );
    }

    #[Test]
    public function it_exposes_its_missing_behavior_and_default_message(): void
    {
        $rule = new RequiredWith('other');

        self::assertTrue($rule->validatesMissing);
        self::assertSame('{input} is required when {other} is present', $rule->message);
    }

    #[Test]
    public function it_preserves_custom_message_keys_and_parameters(): void
    {
        $rule = new RequiredWith('other', message: 'validation.custom');

        self::assertSame('validation.custom', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: 'validation.custom', parameters: ['other' => 'other'])],
            $rule->validate(Missing::Value, new ValidationContext(['other' => 1])),
        );
    }

    #[Test]
    public function it_uses_local_nested_context_and_prefixes_item_paths(): void
    {
        $factory = new ValidatorFactory();
        $rule = new RequiredWith('other');
        $validator = $factory->create([
            'items' => new Each(new Nested($factory->create(['current' => [$rule, new StringType()]]))),
        ]);
        $local = ['other' => 1];
        $errors = $validator->validate(['items' => [$local]])->errors;

        self::assertEquals(
            [
                new ValidationError(
                    messageKey: '{input} is required when {other} is present',
                    parameters: ['other' => 'other'],
                    path: ['items', 0, 'current'],
                ),
            ],
            $errors,
        );
        self::assertSame('items.0.current is required when other is present', $errors[0]->message);
    }

    #[Test]
    public function it_passes_parent_context_to_rules_directly_inside_each(): void
    {
        $validator = new ValidatorFactory()->create(['items' => new Each(new RequiredWith('other'))]);
        $errors = $validator->validate(['other' => 1, 'items' => [Missing::Value]])->errors;

        self::assertEquals(
            [
                new ValidationError(
                    messageKey: '{input} is required when {other} is present',
                    parameters: ['other' => 'other'],
                    path: ['items', 0],
                ),
            ],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_absent_key_through_the_validator(): void
    {
        $validator = new ValidatorFactory()->create(['current' => new RequiredWith('other')]);

        self::assertFalse($validator->validate(['other' => 1])->valid());
    }

    #[Test]
    public function it_uses_literal_field_keys(): void
    {
        $rule = new RequiredWith('other.key');

        self::assertEquals(
            [
                new ValidationError(messageKey: '{input} is required when {other} is present', parameters: [
                    'other' => 'other.key',
                ]),
            ],
            $rule->validate(Missing::Value, new ValidationContext(['other.key' => 1, 'other' => ['key' => 1]])),
        );
    }

    #[Test]
    public function it_accepts_integer_field_keys(): void
    {
        self::assertEquals(
            [
                new ValidationError(messageKey: '{input} is required when {other} is present', parameters: [
                    'other' => 0,
                ]),
            ],
            new RequiredWith(0)->validate(Missing::Value, new ValidationContext([0 => 1])),
        );
    }
}
