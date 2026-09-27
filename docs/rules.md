---
id: rules
title: Rules
sidebar_position: 4
description: The rules Dirthara Validation ships, and how to write your own.
---

## Available rules

All rules live in `Dirthara\Validation\Rule`. The message in each table is the rule's default.

### Presence

| Rule | Passes when | Default message |
|------|-------------|-----------------|
| `Required` | The value is present and not `null`. | `{input} is required` |
| `Present` | The value is present, even when it is `null`. | `{input} must be present` |
| `Nullable` | Never fails. Makes the field pass for `null`. | `{input} may be null` |

### Type

| Rule | Passes when | Default message |
|------|-------------|-----------------|
| `StringType` | The value is a string. | `{input} must be a string` |
| `IntegerType` | The value is an integer. | `{input} must be an integer` |
| `FloatType` | The value is a float. An integer is not a float. | `{input} must be a float` |
| `BooleanType` | The value is `true` or `false`. | `{input} must be a boolean` |
| `Numeric` | The value is an integer, a float, or a numeric string. | `{input} must be numeric` |
| `ArrayType` | The value is an array. | `{input} must be an array` |
| `IterableType` | The value is an array or a `Traversable`. | `{input} must be iterable` |

### String

| Rule | Passes when | Default message |
|------|-------------|-----------------|
| `Email` | The value is a string that is a valid email address. | `{input} must be a valid email address` |
| `Length(int $length)` | The value is a string of exactly that many characters. | `{input} must be exactly {length} characters long` |
| `MinLength(int $minimum)` | The value is a string of at least that many characters. | `{input} must be at least {minimum} characters long` |
| `MaxLength(int $maximum)` | The value is a string of at most that many characters. | `{input} must be at most {maximum} characters long` |
| `Url` | The value is a string that is a valid URL. | `{input} must be a valid URL` |

### Numeric

| Rule | Passes when | Default message |
|------|-------------|-----------------|

### Choice

| Rule | Passes when | Default message |
|------|-------------|-----------------|

### Collections

| Rule | Passes when | Default message |
|------|-------------|-----------------|
| `Each(Rule\|list<Rule>)` | The value is iterable and every item passes the rules. | `{input} must be iterable` |

### Structure

| Rule | Passes when | Default message |
|------|-------------|-----------------|
| `Nested(Validator)` | The value is an array that passes the nested validator. | `{input} must be an array` |

No rule converts a value: a numeric string such as `"18"` is a string, not a number, and fails the numeric rules.

Every rule takes a `message` argument that replaces its default, as in `new Email(message: '{input} is not an email')`.
The message in the table is the one the rule itself reports. `Each` and `Nested` also pass on the errors of the rules
they contain. See [missing and null values](validating-input.md#missing-and-null-values) for how `Required`,
`Present`, and `Nullable` combine.

:::note
`Email` uses PHP's `FILTER_VALIDATE_EMAIL`, which only accepts ASCII addresses. An internationalised address, such as
one with a non-ASCII local part, fails.
:::

## Validate every item of a list

`Each` applies its rules to every item of an iterable value. The error of an item is prefixed with the item's key, so
the second item of `tags` reports `tags.1`.

```php
'tags' => [new Required(), new Each([new Required(), new Email()])],
```

A key that is neither a string nor an integer, which only a generator can produce, is replaced by the item's position.

## Validate nested input

`Nested` validates an array value with a validator of its own. Combine it with `Each` to validate a list of arrays.

```php
$address = $factory->create([
    'street' => new Required(),
    'city' => new Required(),
]);

$validator = $factory->create([
    'address' => [new Required(), new Nested($address)],
    'previous_addresses' => new Each(new Nested($address)),
]);
```

`Nested` accepts only arrays. An object, including one that is iterable or implements `ArrayAccess`, fails with the
`array` error.

## Write your own rule

A rule implements `Dirthara\Validation\Contract\Rule` and returns a list of errors, which is empty when the value
passes. It never receives a missing value, but it does receive `null`, which it should reject unless `null` is valid
for it. Users accept `null` for a field by adding `Nullable` to its rules.

Every rule also exposes its message as a `message` property, which the `Rule` contract declares. Use it as the
`messageKey` of the errors the rule reports, and take it as a constructor argument with your default, so users can
replace it.

```php
use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;

final readonly class MinLength implements Rule
{
    public function __construct(
        private int $minimum,
        public string $message = '{input} must be at least {minimum} characters',
    ) {}

    public function validate(mixed $value): array
    {
        if (is_string($value) && mb_strlen($value) >= $this->minimum) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['minimum' => $this->minimum])];
    }
}
```

The message is plain English with placeholders in braces. `{input}` is filled in with the field, and every other
placeholder with the parameter of the same name. See [messages and translation](results-and-errors.md#messages-and-translation).

Leave the path of the error empty. The validator adds the field, and `Each` and `Nested` add their keys, on the way
back up.

## Validate a missing value yourself

A rule that has to decide what a missing value means, such as a conditional required rule, implements
`Dirthara\Validation\Contract\ValidatesMissing` instead of `Rule`. It then also runs for a missing field, in its place
among the other rules, and receives `Dirthara\Validation\Missing::Value` as the value.

```php
use Dirthara\Validation\Missing;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Contract\ValidatesMissing;

final readonly class RequiredWhen implements ValidatesMissing
{
    public function __construct(
        private bool $condition,
        public string $message = '{input} is required',
    ) {}

    public function validate(mixed $value): array
    {
        if (!$this->condition || !$value instanceof Missing) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message)];
    }
}
```

## Accept a value outright

A rule that makes a field pass for certain values, the way `Nullable` does for `null`, implements
`Dirthara\Validation\Contract\AcceptsValue`. Before any rule runs, every such rule in the field's list is asked
whether it `accepts()` the value. If one does, the field passes and no other rule runs, wherever the accepting rule
stands in the list. Its `validate()` runs like any other rule's for values it does not accept.

```php
use Dirthara\Validation\Contract\AcceptsValue;

final readonly class AllowEmptyString implements AcceptsValue
{
    public function __construct(
        public string $message = '{input} may be empty',
    ) {}

    public function accepts(mixed $value): bool
    {
        return $value === '';
    }

    public function validate(mixed $value): array
    {
        return [];
    }
}
```
