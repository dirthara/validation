<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule;

use stdClass;
use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Rule\Each;
use Dirthara\Validation\Rule\Email;
use Dirthara\Validation\Rule\Nested;
use Dirthara\Validation\Rule\Required;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidatorFactory;
use Dirthara\Validation\Tests\Fixtures\FailingRule;
use Dirthara\Validation\Exception\InvalidRuleException;

final class EachTest extends TestCase
{
    #[Test]
    public function it_accepts_items_that_pass_every_rule(): void
    {
        self::assertSame([], new Each(new Email())->validate(['a@example.com', 'b@example.com']));
        self::assertSame([], new Each(new Email())->validate([]));
    }

    #[Test]
    public function it_rejects_a_value_that_is_not_iterable(): void
    {
        self::assertEquals(
            [new ValidationError(message: 'The value must be iterable.', code: 'iterable')],
            new Each(new Email())->validate('a@example.com'),
        );
    }

    #[Test]
    public function it_prefixes_each_error_with_the_key_of_its_item(): void
    {
        $errors = new Each([new Email(), new FailingRule('inner')])->validate(['invalid', 'first' => 'a@example.com']);

        self::assertEquals(
            [
                new ValidationError(message: 'The value must be a valid email address.', code: 'email', path: [0]),
                new ValidationError(message: 'The value failed.', code: 'failing', path: ['first', 'inner']),
            ],
            $errors,
        );
    }

    #[Test]
    public function it_validates_the_items_of_any_iterable(): void
    {
        $errors = new Each(new Email())->validate(new ArrayIterator(['a@example.com', 'invalid']));

        self::assertEquals(
            [new ValidationError(message: 'The value must be a valid email address.', code: 'email', path: [1])],
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
            [new ValidationError(message: 'The value must be a valid email address.', code: 'email', path: [1])],
            new Each(new Email())->validate($items),
        );
    }

    #[Test]
    public function it_skips_a_null_item_unless_it_is_required(): void
    {
        self::assertSame([], new Each(new Email())->validate(['a@example.com', null]));
        self::assertEquals(
            [new ValidationError(message: 'The value is required.', code: 'required', path: [1])],
            new Each([new Required(), new Email()])->validate(['a@example.com', null]),
        );
    }

    #[Test]
    public function it_validates_each_item_with_a_nested_validator(): void
    {
        $each = new Each(new Nested(new ValidatorFactory()->create(['email' => [new Required(), new Email()]])));

        $errors = $each->validate([['email' => 'a@example.com'], ['email' => 'invalid'], [], 'not an array', null]);

        self::assertEquals(
            [
                new ValidationError(
                    message: 'The value must be a valid email address.',
                    code: 'email',
                    path: [1, 'email'],
                ),
                new ValidationError(message: 'The value is required.', code: 'required', path: [2, 'email']),
                new ValidationError(message: 'The value must be an array.', code: 'array', path: [3]),
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
}
