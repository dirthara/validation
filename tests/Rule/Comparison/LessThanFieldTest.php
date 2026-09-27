<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Comparison;

use stdClass;
use TypeError;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidatorFactory;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Rule\Collection\Each;
use Dirthara\Validation\Rule\Type\StringType;
use Dirthara\Validation\Rule\Structure\Nested;
use PHPUnit\Framework\Attributes\DataProvider;
use Dirthara\Validation\Rule\Comparison\LessThanField;

use const INF;
use const NAN;

final class LessThanFieldTest extends TestCase
{
    /**
     * @return iterable<string, array{mixed, array<string, mixed>}>
     */
    public static function validValues(): iterable
    {
        yield 'less' => [9, ['other' => 10]];
        yield 'fraction below' => [9.5, ['other' => 10]];
        yield 'float reference' => [10, ['other' => 10.5]];
        yield 'negative' => [-2, ['other' => -1]];
        yield 'negative infinity' => [-INF, ['other' => 10]];
    }

    /**
     * @param array<string, mixed> $input
     */
    #[Test]
    #[DataProvider('validValues')]
    public function it_accepts_valid_values(mixed $value, array $input): void
    {
        $rule = new LessThanField('other');

        self::assertSame([], $rule->validate($value, new ValidationContext($input)));
    }

    /**
     * @return iterable<string, array{mixed, array<string, mixed>}>
     */
    public static function invalidValues(): iterable
    {
        yield 'greater' => [11, ['other' => 10]];
        yield 'equal' => [10, ['other' => 10]];
        yield 'equal float' => [10.0, ['other' => 10]];
        yield 'fraction above' => [10.5, ['other' => 10]];
        yield 'zero' => [0, ['other' => 0.0]];
        yield 'nan current' => [NAN, ['other' => 10]];
        yield 'nan reference' => [10, ['other' => NAN]];
        yield 'positive infinity' => [INF, ['other' => 10]];
        yield 'current null' => [null, ['other' => 10]];
        yield 'reference null' => [10, ['other' => null]];
        yield 'current numeric string' => ['10', ['other' => 10]];
        yield 'reference numeric string' => [10, ['other' => '10']];
        yield 'current boolean' => [false, ['other' => 10]];
        yield 'reference boolean' => [10, ['other' => false]];
        yield 'current array' => [[], ['other' => 10]];
        yield 'reference array' => [10, ['other' => []]];
        yield 'current object' => [new stdClass(), ['other' => 10]];
        yield 'reference object' => [10, ['other' => new stdClass()]];
        yield 'current date' => [new DateTimeImmutable('2026-01-01'), ['other' => 10]];
        yield 'reference date' => [10, ['other' => new DateTimeImmutable('2026-01-01')]];
        yield 'missing reference' => [10, []];
    }

    /**
     * @param array<string, mixed> $input
     */
    #[Test]
    #[DataProvider('invalidValues')]
    public function it_rejects_invalid_values(mixed $value, array $input): void
    {
        $rule = new LessThanField('other');

        self::assertEquals(
            [
                new ValidationError(messageKey: '{input} must be less than {other}', parameters: ['other' => 'other']),
            ],
            $rule->validate($value, new ValidationContext($input)),
        );
    }

    #[Test]
    public function it_exposes_its_missing_behavior_and_default_message(): void
    {
        $rule = new LessThanField('other');

        self::assertFalse($rule->validatesMissing);
        self::assertSame('{input} must be less than {other}', $rule->message);
    }

    #[Test]
    public function it_preserves_custom_message_keys_and_parameters(): void
    {
        $rule = new LessThanField('other', message: 'validation.custom');

        self::assertSame('validation.custom', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: 'validation.custom', parameters: ['other' => 'other'])],
            $rule->validate(10, new ValidationContext(['other' => 5])),
        );
    }

    #[Test]
    public function it_uses_local_nested_context_and_prefixes_item_paths(): void
    {
        $factory = new ValidatorFactory();
        $rule = new LessThanField('other');
        $validator = $factory->create([
            'items' => new Each(new Nested($factory->create(['current' => [$rule, new StringType()]]))),
        ]);
        $local = ['other' => 5];
        $local['current'] = 10;
        $errors = $validator->validate(['other' => 20, 'items' => [$local]])->errors;

        self::assertEquals(
            [
                new ValidationError(
                    messageKey: '{input} must be less than {other}',
                    parameters: ['other' => 'other'],
                    path: ['items', 0, 'current'],
                ),
            ],
            $errors,
        );
        self::assertSame('items.0.current must be less than other', $errors[0]->message);
    }

    #[Test]
    public function it_passes_parent_context_to_rules_directly_inside_each(): void
    {
        $validator = new ValidatorFactory()->create(['items' => new Each(new LessThanField('other'))]);
        $errors = $validator->validate(['other' => 5, 'items' => [10]])->errors;

        self::assertEquals(
            [
                new ValidationError(
                    messageKey: '{input} must be less than {other}',
                    parameters: ['other' => 'other'],
                    path: ['items', 0],
                ),
            ],
            $errors,
        );
    }

    #[Test]
    public function it_skips_a_missing_current_field(): void
    {
        $validator = new ValidatorFactory()->create(['current' => new LessThanField('other')]);

        self::assertSame([], $validator->validate([])->errors);
        self::assertSame([], $validator->validate(['other' => null])->errors);
        self::assertSame([], $validator->validate(['other' => 10])->errors);
    }

    #[Test]
    public function it_uses_literal_field_keys(): void
    {
        $rule = new LessThanField('other.key');

        self::assertEquals(
            [
                new ValidationError(messageKey: '{input} must be less than {other}', parameters: [
                    'other' => 'other.key',
                ]),
            ],
            $rule->validate(10, new ValidationContext(['other.key' => 5, 'other' => ['key' => 1]])),
        );
    }

    #[Test]
    public function it_rejects_integer_field_references(): void
    {
        $this->expectException(TypeError::class);

        // @mago-expect analysis:invalid-argument Integer field references are outside the contract
        new LessThanField(0);
    }
}
