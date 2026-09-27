<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule\Collection;

use stdClass;
use ArrayIterator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\Rule\Text\Email;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidatorFactory;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Rule\Collection\Each;
use Dirthara\Validation\Rule\Structure\Nested;
use Dirthara\Validation\Rule\Presence\Nullable;
use Dirthara\Validation\Rule\Presence\Required;
use Dirthara\Validation\Tests\Fixtures\FailingRule;
use Dirthara\Validation\Exception\InvalidRuleException;

final class EachTest extends TestCase
{
    #[Test]
    public function it_accepts_items_that_pass_every_rule(): void
    {
        self::assertSame(
            [],
            new Each(new Email())->validate(
                context: new ValidationContext([]),
                value: ['a@example.com', 'b@example.com'],
            ),
        );
        self::assertSame([], new Each(new Email())->validate(context: new ValidationContext([]), value: []));
    }

    #[Test]
    public function it_rejects_a_value_that_is_not_iterable(): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be iterable')],
            new Each(new Email())->validate(context: new ValidationContext([]), value: 'a@example.com'),
        );
    }

    #[Test]
    public function it_prefixes_each_error_with_the_key_of_its_item(): void
    {
        $errors = new Each([new Email(), new FailingRule('inner')])->validate(
            context: new ValidationContext([]),
            value: ['invalid', 'first' => 'a@example.com'],
        );

        self::assertEquals(
            [
                new ValidationError(messageKey: '{input} must be a valid email address', path: [0]),
                new ValidationError(messageKey: '{input} failed', path: ['first', 'inner']),
            ],
            $errors,
        );
    }

    #[Test]
    public function it_validates_the_items_of_any_iterable(): void
    {
        $errors = new Each(new Email())->validate(
            context: new ValidationContext([]),
            value: new ArrayIterator(['a@example.com', 'invalid']),
        );

        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be a valid email address', path: [1])],
            $errors,
        );
    }

    #[Test]
    public function it_uses_the_position_of_an_item_whose_key_is_not_a_string_or_an_integer(): void
    {
        $items = (static function (): iterable {
            yield 'a@example.com' => 'a@example.com';
            yield new stdClass() => 'invalid';
        })();

        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be a valid email address', path: [1])],
            new Each(new Email())->validate(context: new ValidationContext([]), value: $items),
        );
    }

    #[Test]
    public function it_passes_a_null_item_to_its_rules(): void
    {
        self::assertEquals(
            [new ValidationError(messageKey: '{input} must be a valid email address', path: [1])],
            new Each(new Email())->validate(context: new ValidationContext([]), value: ['a@example.com', null]),
        );
        self::assertSame(
            [],
            new Each([new Nullable(), new Email()])->validate(
                context: new ValidationContext([]),
                value: ['a@example.com', null],
            ),
        );
    }

    #[Test]
    public function it_validates_each_item_with_a_nested_validator(): void
    {
        $each = new Each(new Nested(new ValidatorFactory()->create(['email' => [new Required(), new Email()]])));

        $errors = $each->validate(context: new ValidationContext([]), value: [
            ['email' => 'a@example.com'],
            ['email' => 'invalid'],
            [],
            'not an array',
            null,
        ]);

        self::assertEquals(
            [
                new ValidationError(messageKey: '{input} must be a valid email address', path: [1, 'email']),
                new ValidationError(messageKey: '{input} is required', path: [2, 'email']),
                new ValidationError(messageKey: '{input} must be an array', path: [3]),
                new ValidationError(messageKey: '{input} must be an array', path: [4]),
            ],
            $errors,
        );
    }

    #[Test]
    public function it_rejects_an_entry_that_is_not_a_rule(): void
    {
        $this->expectException(InvalidRuleException::class);

        // @mago-expect analysis:possibly-invalid-argument The invalid rule is the point of the test
        new Each([new Email(), 'required']);
    }

    #[Test]
    public function it_has_a_default_message(): void
    {
        self::assertSame('{input} must be iterable', new Each(new Email())->message);
    }

    #[Test]
    public function it_uses_a_custom_message_for_its_error(): void
    {
        $rule = new Each(new Email(), message: '{input} is wrong');

        self::assertSame('{input} is wrong', $rule->message);
        self::assertEquals(
            [new ValidationError(messageKey: '{input} is wrong')],
            $rule->validate(context: new ValidationContext([]), value: 'invalid'),
        );
    }
}
