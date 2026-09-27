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
use Dirthara\Validation\Rule\Presence\RequiredWithoutAll;

final class RequiredWithoutAllTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed, array<string, mixed>}>
     */
    public static function validValues(): iterable
    {
        yield 'both missing reference and value' => ['supplied', []];
        yield 'both null reference and value' => ['supplied', ['other' => null, 'second' => null]];
        yield 'both present reference and missing' => [Missing::Value, ['other' => 1, 'second' => 2]];
        yield 'both present reference and null' => [null, ['other' => 1, 'second' => 2]];
        yield 'both present reference and value' => ['supplied', ['other' => 1, 'second' => 2]];
        yield 'first missing reference and missing' => [Missing::Value, ['second' => 2]];
        yield 'first missing reference and null' => [null, ['second' => 2]];
        yield 'first missing reference and value' => ['supplied', ['second' => 2]];
        yield 'second missing reference and missing' => [Missing::Value, ['other' => 1]];
        yield 'second missing reference and null' => [null, ['other' => 1]];
        yield 'second missing reference and value' => ['supplied', ['other' => 1]];
        yield 'first null reference and missing' => [Missing::Value, ['other' => null, 'second' => 2]];
        yield 'first null reference and null' => [null, ['other' => null, 'second' => 2]];
        yield 'first null reference and value' => ['supplied', ['other' => null, 'second' => 2]];
        yield 'second null reference and missing' => [Missing::Value, ['other' => 1, 'second' => null]];
        yield 'second null reference and null' => [null, ['other' => 1, 'second' => null]];
        yield 'second null reference and value' => ['supplied', ['other' => 1, 'second' => null]];
        yield 'null and missing reference and value' => ['supplied', ['other' => null]];
        yield 'false and zero reference and missing' => [Missing::Value, ['other' => false, 'second' => 0]];
        yield 'false and zero reference and null' => [null, ['other' => false, 'second' => 0]];
        yield 'false and zero reference and value' => ['supplied', ['other' => false, 'second' => 0]];
        yield 'empty values reference and missing' => [Missing::Value, ['other' => '', 'second' => []]];
        yield 'empty values reference and null' => [null, ['other' => '', 'second' => []]];
        yield 'empty values reference and false' => [false, ['other' => '', 'second' => []]];
        yield 'empty values reference and zero' => [0, ['other' => '', 'second' => []]];
        yield 'empty values reference and empty string' => ['', ['other' => '', 'second' => []]];
        yield 'empty values reference and empty array' => [[], ['other' => '', 'second' => []]];
        yield 'empty values reference and value' => ['supplied', ['other' => '', 'second' => []]];
    }

    /**
     * @param array<string, mixed> $input
     */
    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_valid_values(mixed $value, array $input): void
    {
        $rule = new RequiredWithoutAll(['other', 'second']);

        self::assertSame([], $rule->validate($value, new ValidationContext($input)));
    }

    /**
     * @return iterable<string, array{mixed, array<string, mixed>}>
     */
    public static function invalidValues(): iterable
    {
        yield 'both missing reference and missing' => [Missing::Value, []];
        yield 'both missing reference and null' => [null, []];
        yield 'both null reference and missing' => [Missing::Value, ['other' => null, 'second' => null]];
        yield 'both null reference and null' => [null, ['other' => null, 'second' => null]];
        yield 'null and missing reference and missing' => [Missing::Value, ['other' => null]];
        yield 'null and missing reference and null' => [null, ['other' => null]];
    }

    /**
     * @param array<string, mixed> $input
     */
    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_invalid_values(mixed $value, array $input): void
    {
        $rule = new RequiredWithoutAll(['other', 'second']);

        self::assertEquals(
            [
                new ValidationError(messageKey: '{input} is required when none of {others} are present', parameters: ['others' => [
                    'other',
                    'second',
                ]]),
            ],
            $rule->validate($value, new ValidationContext($input)),
        );
    }

    #[Test]
    public function it_exposes_its_missing_behavior_and_default_message(): void
    {
        $rule = new RequiredWithoutAll(['other', 'second']);

        self::assertTrue($rule->validatesMissing);
        self::assertSame('{input} is required when none of {others} are present', $rule->message);
    }

    #[Test]
    public function it_preserves_custom_message_keys_and_parameters(): void
    {
        $rule = new RequiredWithoutAll(['other', 'second'], message: 'validation.custom');

        self::assertSame('validation.custom', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: 'validation.custom', parameters: ['others' => ['other', 'second']])],
            $rule->validate(Missing::Value, new ValidationContext([])),
        );
    }

    #[Test]
    public function it_uses_local_nested_context_and_prefixes_item_paths(): void
    {
        $factory = new ValidatorFactory();
        $rule = new RequiredWithoutAll(['other', 'second']);
        $validator = $factory->create([
            'items' => new Each(new Nested($factory->create(['current' => [$rule, new StringType()]]))),
        ]);
        $local = [];
        $errors = $validator->validate(['other' => 1, 'second' => 2, 'items' => [$local]])->errors;

        self::assertEquals(
            [
                new ValidationError(
                    messageKey: '{input} is required when none of {others} are present',
                    parameters: ['others' => ['other', 'second']],
                    path: ['items', 0, 'current'],
                ),
            ],
            $errors,
        );
        self::assertSame('items.0.current is required when none of other, second are present', $errors[0]->message);
    }

    #[Test]
    public function it_passes_parent_context_to_rules_directly_inside_each(): void
    {
        $validator = new ValidatorFactory()->create(['items' => new Each(new RequiredWithoutAll(['other', 'second']))]);
        $errors = $validator->validate(['items' => [Missing::Value]])->errors;

        self::assertEquals(
            [
                new ValidationError(
                    messageKey: '{input} is required when none of {others} are present',
                    parameters: ['others' => ['other', 'second']],
                    path: ['items', 0],
                ),
            ],
            $errors,
        );
    }

    #[Test]
    public function it_validates_an_absent_key_through_the_validator(): void
    {
        $validator = new ValidatorFactory()->create(['current' => new RequiredWithoutAll(['other', 'second'])]);

        self::assertFalse($validator->validate([])->valid());
    }

    #[Test]
    public function it_uses_literal_field_keys(): void
    {
        $rule = new RequiredWithoutAll(['second', 'other.key']);

        self::assertEquals(
            [
                new ValidationError(messageKey: '{input} is required when none of {others} are present', parameters: ['others' => [
                    'second',
                    'other.key',
                ]]),
            ],
            $rule->validate(Missing::Value, new ValidationContext(['other' => ['key' => 1]])),
        );
    }

    #[Test]
    public function it_requires_a_value_when_no_fields_are_configured(): void
    {
        $rule = new RequiredWithoutAll([]);

        self::assertEquals(
            [
                new ValidationError(messageKey: '{input} is required when none of {others} are present', parameters: ['others' => []]),
            ],
            $rule->validate(Missing::Value, new ValidationContext([])),
        );
        self::assertSame([], $rule->validate(false, new ValidationContext([])));
    }
}
