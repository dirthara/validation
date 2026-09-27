<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Rule\Each;
use Dirthara\Validation\Rule\Email;
use Dirthara\Validation\Rule\Nested;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidatorFactory;
use Dirthara\Validation\Tests\Fixtures\FailingRule;
use Dirthara\Validation\Tests\Fixtures\RequiredRule;
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
    public function it_skips_a_missing_or_null_value(): void
    {
        self::assertSame([], new Each(new FailingRule())->validate(['item'], present: false));
        self::assertSame([], new Each(new FailingRule())->validate(null));
    }

    #[Test]
    public function it_rejects_a_value_that_is_not_iterable(): void
    {
        self::assertEquals(
            [new ValidationError(field: '', message: 'The value must be iterable.', code: 'iterable')],
            new Each(new Email())->validate('a@example.com'),
        );
    }

    #[Test]
    public function it_prefixes_each_error_with_the_key_of_its_item(): void
    {
        $errors = new Each([new Email(), new FailingRule('inner')])->validate(['a@example.com', 'first' => 'invalid']);

        self::assertEquals(
            [
                new ValidationError(field: '0.inner', message: 'The value failed.', code: 'failing'),
                new ValidationError(field: 'first', message: 'The value must be a valid email address.', code: 'email'),
                new ValidationError(field: 'first.inner', message: 'The value failed.', code: 'failing'),
            ],
            $errors,
        );
    }

    #[Test]
    public function it_validates_the_items_of_any_iterable(): void
    {
        $errors = new Each(new Email())->validate(new ArrayIterator(['a@example.com', 'invalid']));

        self::assertEquals(
            [new ValidationError(field: '1', message: 'The value must be a valid email address.', code: 'email')],
            $errors,
        );
    }

    #[Test]
    public function it_treats_every_item_as_present(): void
    {
        $errors = new Each(new RequiredRule())->validate(['value', null]);

        self::assertEquals(
            [new ValidationError(field: '1', message: 'The value is required.', code: 'required')],
            $errors,
        );
    }

    #[Test]
    public function it_validates_each_item_with_a_nested_validator(): void
    {
        $each = new Each(new Nested(new ValidatorFactory()->create(['email' => [new RequiredRule(), new Email()]])));

        $errors = $each->validate([['email' => 'a@example.com'], ['email' => 'invalid'], [], 'not an array', null]);

        self::assertEquals(
            [
                new ValidationError(
                    field: '1.email',
                    message: 'The value must be a valid email address.',
                    code: 'email',
                ),
                new ValidationError(field: '2.email', message: 'The value is required.', code: 'required'),
                new ValidationError(field: '3', message: 'The value must be an array.', code: 'array'),
            ],
            $errors,
        );
    }

    #[Test]
    public function it_rejects_an_entry_that_is_not_a_rule(): void
    {
        $this->expectException(InvalidRuleException::class);

        // @mago-expect analysis:possibly-invalid-argument -- the invalid rule is the point of the test
        new Each([new Email(), 'required']);
    }
}
