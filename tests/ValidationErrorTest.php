<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests;

use Error;
use stdClass;
use Stringable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

final class ValidationErrorTest extends TestCase
{
    #[Test]
    public function it_carries_its_message_key_parameters_and_path(): void
    {
        $error = new ValidationError(
            messageKey: '{input} must be at least {minimum}',
            parameters: ['minimum' => 18],
            path: ['users', 0, 'age'],
        );

        self::assertSame('{input} must be at least {minimum}', $error->messageKey);
        self::assertSame(['minimum' => 18], $error->parameters);
        self::assertSame(['users', 0, 'age'], $error->path);
        self::assertSame('users.0.age', $error->field);
        self::assertSame('users.0.age must be at least 18', $error->message);
    }

    #[Test]
    public function it_has_no_parameters_and_no_path_by_default(): void
    {
        $error = new ValidationError(messageKey: '{input} is required');

        self::assertSame([], $error->parameters);
        self::assertSame([], $error->path);
        self::assertSame('', $error->field);
    }

    #[Test]
    public function it_renders_the_input_as_its_field(): void
    {
        self::assertSame('email is required', new ValidationError('{input} is required', path: ['email'])->message);
    }

    #[Test]
    public function it_renders_the_input_as_input_without_a_path(): void
    {
        self::assertSame('input is required', new ValidationError('{input} is required')->message);
    }

    #[Test]
    public function it_leaves_a_message_key_without_placeholders_as_it_is(): void
    {
        self::assertSame('Choose another name', new ValidationError('Choose another name', path: ['name'])->message);
    }

    #[Test]
    public function it_leaves_a_placeholder_without_a_parameter_as_it_is(): void
    {
        self::assertSame(
            'age must be at least {minimum}',
            new ValidationError('{input} must be at least {minimum}', path: ['age'])->message,
        );
    }

    #[Test]
    public function it_lets_a_parameter_override_the_input(): void
    {
        $error = new ValidationError('{input} is required', parameters: ['input' => 'Email address'], path: ['email']);

        self::assertSame('Email address is required', $error->message);
        self::assertSame('email', $error->field);
    }

    /**
     * @return iterable<string, array{mixed, string}>
     */
    public static function parameters(): iterable
    {
        yield 'string' => ['text', 'text'];
        yield 'integer' => [18, '18'];
        yield 'float' => [1.5, '1.5'];
        yield 'true' => [true, 'true'];
        yield 'false' => [false, 'false'];
        yield 'null' => [null, 'null'];
        yield 'stringable' => [
            new class() implements Stringable {
                public function __toString(): string
                {
                    return 'stringable';
                }
            },
            'stringable',
        ];
        yield 'list' => [[1, 2], '1, 2'];
        yield 'mixed list' => [['a', true, null, 1.5], 'a, true, null, 1.5'];
        yield 'nested list' => [[1, [2, 3]], '1, 2, 3'];
        yield 'empty array' => [[], ''];
        yield 'object' => [new stdClass(), 'stdClass'];
    }

    #[Test]
    #[DataProvider('parameters')]
    public function it_renders_each_kind_of_parameter(mixed $parameter, string $rendered): void
    {
        $error = new ValidationError('{input} is {value}', parameters: ['value' => $parameter], path: ['x']);

        self::assertSame('x is ' . $rendered, $error->message);
    }

    #[Test]
    public function it_prepends_a_prefix_to_its_path_and_keeps_its_message_key_and_parameters(): void
    {
        $error = new ValidationError('{input} must be at least {minimum}', parameters: ['minimum' => 18]);
        $prefixed = $error->prefix('age')->prefix(0)->prefix('users');

        self::assertNotSame($error, $prefixed);
        self::assertSame([], $error->path);
        self::assertSame(['users', 0, 'age'], $prefixed->path);
        self::assertSame('users.0.age', $prefixed->field);
        self::assertSame('{input} must be at least {minimum}', $prefixed->messageKey);
        self::assertSame(['minimum' => 18], $prefixed->parameters);
        self::assertSame('users.0.age must be at least 18', $prefixed->message);
    }

    #[Test]
    public function it_keeps_a_segment_that_contains_a_dot_intact_in_its_path(): void
    {
        $error = new ValidationError('{input} is invalid', path: ['a.b'])->prefix('settings');

        self::assertSame(['settings', 'a.b'], $error->path);
        self::assertSame('settings.a.b', $error->field);
    }

    #[Test]
    public function it_cannot_have_its_field_written(): void
    {
        $error = new ValidationError('{input} is invalid', path: ['email']);

        $this->expectException(Error::class);

        // @mago-expect analysis:invalid-property-write Writing the field is the point of the test
        $error->field = 'other';
    }

    #[Test]
    public function it_cannot_have_its_message_written(): void
    {
        $error = new ValidationError('{input} is invalid', path: ['email']);

        $this->expectException(Error::class);

        // @mago-expect analysis:invalid-property-write Writing the message is the point of the test
        $error->message = 'other';
    }

    #[Test]
    public function it_cannot_have_its_path_written(): void
    {
        $error = new ValidationError('{input} is invalid', path: ['email']);

        $this->expectException(Error::class);

        // @mago-expect analysis:invalid-property-write Writing the path is the point of the test
        $error->path = ['other'];
    }
}
