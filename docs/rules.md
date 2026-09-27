---
id: rules
title: Rules
sidebar_position: 4
description: The rules Dirthara Validation ships, and how to write your own.
---

## Available rules

Rules are grouped by what they check, each group in its own namespace under `Dirthara\Validation\Rule`. The message
in each table is the rule's default.

### Presence

In `Dirthara\Validation\Rule\Presence`.

| Rule | Passes when | Default message |
|------|-------------|-----------------|
| `Required` | The value is present and not `null`. | `{input} is required` |
| `Present` | The value is present, even when it is `null`. | `{input} must be present` |
| `Nullable` | Never fails. Makes the field pass for `null`. | `{input} may be null` |
| `RequiredIf(string\|int $field, mixed $value)` | Like `Required`, but only when the other field is identical to the value. | `{input} is required when {other} is {value}` |
| `RequiredUnless(string\|int $field, mixed $value)` | Like `Required`, unless the other field is identical to the value. | `{input} is required unless {other} is {value}` |
| `RequiredWith(string\|int $field)` | The current value exists and is non-null, when the other field exists and is non-null (even `false`, `0`, `''`, or `[]`). | `{input} is required when {other} is present` |
| `RequiredWithout(string\|int $field)` | The current value exists and is non-null, when the other field is missing or null. | `{input} is required when {other} is not present` |
| `ProhibitedWith(string\|int $field)` | The current key is absent, including no explicit `null`, when the other field exists and is non-null (even `false`, `0`, `''`, or `[]`). | `{input} is prohibited when {other} is present` |
| `ProhibitedWithout(string\|int $field)` | The current key is absent, including no explicit `null`, when the other field is missing or null. | `{input} is prohibited when {other} is not present` |
| `PresentIf(string\|int $field, mixed $value)` | The current key exists (explicit `null` is allowed), when the other field strictly equals the configured value. | `{input} must be present when {other} is {value}` |
| `PresentUnless(string\|int $field, mixed $value)` | The current key exists (explicit `null` is allowed), unless the other field strictly equals the configured value. | `{input} must be present unless {other} is {value}` |
| `ProhibitedIf(string\|int $field, mixed $value)` | The current key is absent (supplied `null`, `false`, `0`, `''`, and `[]` fail), when the other field strictly equals the configured value. | `{input} is prohibited when {other} is {value}` |
| `ProhibitedUnless(string\|int $field, mixed $value)` | The current key is absent (supplied `null`, `false`, `0`, `''`, and `[]` fail), unless the other field strictly equals the configured value. | `{input} is prohibited unless {other} is {value}` |

### Type

In `Dirthara\Validation\Rule\Type`.

| Rule | Passes when | Default message |
|------|-------------|-----------------|
| `StringType` | The value is a string. | `{input} must be a string` |
| `IntegerType` | The value is an integer. | `{input} must be an integer` |
| `FloatType` | The value is a float. An integer is not a float. | `{input} must be a float` |
| `BooleanType` | The value is `true` or `false`. | `{input} must be a boolean` |
| `Numeric` | The value is an integer, a float, or a numeric string. | `{input} must be numeric` |
| `ArrayType` | The value is an array. | `{input} must be an array` |
| `IterableType` | The value is an array or a `Traversable`. | `{input} must be iterable` |

### Text

In `Dirthara\Validation\Rule\Text`.

| Rule | Passes when | Default message |
|------|-------------|-----------------|
| `Email` | The value is a string that is a valid email address. | `{input} must be a valid email address` |
| `Length(int $length)` | The value is a string of exactly that many characters. | `{input} must be exactly {length} characters long` |
| `MinLength(int $minimum)` | The value is a string of at least that many characters. | `{input} must be at least {minimum} characters long` |
| `MaxLength(int $maximum)` | The value is a string of at most that many characters. | `{input} must be at most {maximum} characters long` |
| `Url` | The value is a string that is a valid URL. | `{input} must be a valid URL` |
| `Uuid` | The value is a UUID string in its canonical, hyphenated form, in any version. | `{input} must be a valid UUID` |
| `Regex(string $pattern)` | The value is a string that matches the regular expression. | `{input} has an invalid format` |

### Numeric

In `Dirthara\Validation\Rule\Numeric`.

| Rule | Passes when | Default message |
|------|-------------|-----------------|
| `Minimum(int\|float $minimum)` | The value is an integer or a float of at least the minimum. | `{input} must be at least {minimum}` |
| `Maximum(int\|float $maximum)` | The value is an integer or a float of at most the maximum. | `{input} must be at most {maximum}` |
| `Positive` | The value is an integer or a float greater than zero. | `{input} must be positive` |
| `Negative` | The value is an integer or a float less than zero. | `{input} must be negative` |
| `Between(int\|float $minimum, int\|float $maximum)` | The value is an integer or a float from the minimum up to and including the maximum. | `{input} must be between {minimum} and {maximum}` |

### Comparison

In `Dirthara\Validation\Rule\Comparison`.

| Rule | Passes when | Default message |
|------|-------------|-----------------|
| `Same(string\|int $field)` | The value is identical to the other field's value. | `{input} must be the same as {other}` |
| `Different(string\|int $field)` | The value is not identical to the other field's value. | `{input} must be different from {other}` |
| `GreaterThanField(string\|int $field)` | Both values are integers or floats and the current value is greater than the other field's value. Missing or unsupported references fail; a missing current field is skipped. | `{input} must be greater than {other}` |
| `GreaterThanOrEqualField(string\|int $field)` | Both values are integers or floats and the current value is greater than or equal to the other field's value. Missing or unsupported references fail; a missing current field is skipped. | `{input} must be greater than or equal to {other}` |
| `LessThanField(string\|int $field)` | Both values are integers or floats and the current value is less than the other field's value. Missing or unsupported references fail; a missing current field is skipped. | `{input} must be less than {other}` |
| `LessThanOrEqualField(string\|int $field)` | Both values are integers or floats and the current value is less than or equal to the other field's value. Missing or unsupported references fail; a missing current field is skipped. | `{input} must be less than or equal to {other}` |

### Choice

In `Dirthara\Validation\Rule\Choice`.

| Rule | Passes when | Default message |
|------|-------------|-----------------|
| `Choice(list<mixed> $choices)` | The value is one of the choices, compared strictly. | `{input} must be one of {choices}` |
| `NotChoice(list<mixed> $choices)` | The value is none of the choices, compared strictly. | `{input} must not be one of {choices}` |

### Collections

In `Dirthara\Validation\Rule\Collection`.

| Rule | Passes when | Default message |
|------|-------------|-----------------|
| `Each(Rule\|list<Rule>)` | The value is iterable and every item passes the rules. | `{input} must be iterable` |
| `Count(int $count)` | The value is an array or a `Countable` with exactly that many items. | `{input} must contain exactly {count} items` |
| `MinCount(int $minimum)` | The value is an array or a `Countable` with at least that many items. | `{input} must contain at least {minimum} items` |
| `MaxCount(int $maximum)` | The value is an array or a `Countable` with at most that many items. | `{input} must contain at most {maximum} items` |

### Structure

In `Dirthara\Validation\Rule\Structure`.

| Rule | Passes when | Default message |
|------|-------------|-----------------|
| `Nested(Validator)` | The value is an array that passes the nested validator. | `{input} must be an array` |

No rule converts a value: a numeric string such as `"18"` is a string, not a number, and fails the numeric rules.

Every rule takes a `message` argument that replaces its default, as in `new Email(message: '{input} is not an email')`.
The message in the table is the one the rule itself reports. `Each` and `Nested` also pass on the errors of the rules
they contain. See [missing and null values](validating-input.md#missing-and-null-values) for how `Required`,
`Present`, and `Nullable` combine.

A missing reference never satisfies the equality condition of `PresentIf` or `PresentUnless`: `PresentIf` allows
the current key to be absent, while `PresentUnless` requires it. An explicit null reference can match a configured
`null` value.

`ProhibitedIf` and `ProhibitedUnless` also compare strictly. A missing reference leaves `ProhibitedIf` inactive and
makes `ProhibitedUnless` prohibit the current key. These rules check presence, so empty supplied values still fail
when prohibited. Like every rule, they are bypassed if an `AcceptsValue` rule such as `Nullable` accepts the value.

## Compare with other fields

Rules that read another field name it by its literal key and put that key in their error parameters as `other`.
`Same`, `Different`, `RequiredIf`, and `RequiredUnless` use strict comparison (`===`).

```php
$validator = $factory->create([
    'account_type' => [new Required(), new Choice(['personal', 'business'])],
    'company_name' => [new RequiredIf(field: 'account_type', value: 'business'), new StringType()],
    'email' => [new Required(), new Email()],
    'email_confirmation' => [new Required(), new Same('email')],
]);
```

- **Keys are literal.** `'address.street'` is a key with a dot in it, not the `street` field of `address`.
- **The other field is a sibling.** Inside a `Nested` validator, the rules see the fields of the nested array, not those of
  the outer input. The rules of an `Each` item see the fields next to the `Each` field.
- **A missing other field** fails `Same` and `Different`, makes `RequiredIf` not require the field, and makes
  `RequiredUnless` require it.
- **A missing current field** skips `Same` and `Different`, like any rule that does not validate a missing value. Add
  `Required` to make the field itself required.

:::note
`Email` and `Url` use PHP's `FILTER_VALIDATE_EMAIL` and `FILTER_VALIDATE_URL`, which only accept ASCII. An
internationalised address, such as one with a non-ASCII local part or domain, fails. `Url` accepts any scheme, so check
the scheme with `Regex` when only some are allowed.
:::

The length rules count characters, not bytes, so `äöü` is three characters long. A string that is not valid UTF-8 fails
them.

## Validate every item of a list

`Each` applies its rules to every item of an iterable value. The error of an item is prefixed with the item's key, so
the second item of `tags` reports `tags.1`.

```php
'tags' => [new Required(), new Each([new Required(), new Email()])],
```

A key that is neither a string nor an integer, which only a generator can produce, is replaced by the item's position.
The rules of an item that read other fields, such as `Different`, see the fields next to the `Each` field.

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

A rule implements `Dirthara\Validation\Contract\Rule`. Its `validate()` receives the value and a
`Dirthara\Validation\ValidationContext`, and returns a list of errors, which is empty when the value passes. Most rules
only look at the value and ignore the context. A rule receives `null`, which it should reject unless `null` is valid
for it. Users accept `null` for a field by adding `Nullable` to its rules.

The contract declares two properties:

| Property           | Type     | Meaning                                                                           |
|--------------------|----------|-----------------------------------------------------------------------------------|
| `message`          | `string` | The rule's message, used as the `messageKey` of the errors it reports.            |
| `validatesMissing` | `bool`   | Whether the rule also runs for a missing field. See [below](#validate-a-missing-value-yourself). |

Take the message as a constructor argument with your default, so users can replace it. The trait
`Dirthara\Validation\Rule\SkipsMissing` sets `validatesMissing` to `false`, which is what almost every rule wants.

```php
use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Rule\SkipsMissing;

final class MinLength implements Rule
{
    use SkipsMissing;

    public function __construct(
        private readonly int $minimum,
        public readonly string $message = '{input} must be at least {minimum} characters',
    ) {}

    public function validate(mixed $value, ValidationContext $context): array
    {
        if (is_string($value) && mb_strlen($value) >= $this->minimum) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['minimum' => $this->minimum])];
    }
}
```

:::note
The rule classes are `final`, not `final readonly`, because PHP does not allow a property hook such as the one in
`SkipsMissing` in a readonly class. Declare each property `readonly` instead.
:::

The message is plain English with placeholders in braces. `{input}` is filled in with the field, and every other
placeholder with the parameter of the same name. See [messages and translation](results-and-errors.md#messages-and-translation).

Leave the path of the error empty. The validator adds the field, and `Each` and `Nested` add their keys, on the way
back up.

## Validate a missing value yourself

A rule that has to decide what a missing value means sets `validatesMissing` to `true`. It then also runs for a missing
field, in its place among the other rules, and receives `Dirthara\Validation\Missing::Value` as the value.

```php
use Dirthara\Validation\Missing;
use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;

final class RequiredWhen implements Rule
{
    public bool $validatesMissing {
        get => true;
    }

    public function __construct(
        private readonly bool $condition,
        public readonly string $message = '{input} is required',
    ) {}

    public function validate(mixed $value, ValidationContext $context): array
    {
        if (!$this->condition || !$value instanceof Missing) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message)];
    }
}
```

## Read other fields

The context holds the input the field belongs to. `has()` tells whether a key is present, and `value()` returns its
value, or `Missing::Value` when it is not. Keys are literal, and inside a `Nested` validator the context is the nested
array.

```php
use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Rule\SkipsMissing;

final class GreaterIntegerThanField implements Rule
{
    use SkipsMissing;

    public function __construct(
        private readonly string $field,
        public readonly string $message = '{input} must be greater than {other}',
    ) {}

    public function validate(mixed $value, ValidationContext $context): array
    {
        $other = $context->value($this->field);

        if (is_int($value) && is_int($other) && $value > $other) {
            return [];
        }

        return [new ValidationError(messageKey: $this->message, parameters: ['other' => $this->field])];
    }
}
```

A rule that reads other fields can validate a missing value too, the way `RequiredIf` does.

## Accept a value outright

A rule that makes a field pass for certain values, the way `Nullable` does for `null`, implements
`Dirthara\Validation\Contract\AcceptsValue`. Before any rule runs, every such rule in the field's list is asked
whether it `accepts()` the value. If one does, the field passes and no other rule runs, wherever the accepting rule
stands in the list. Its `validate()` runs like any other rule's for values it does not accept.

```php
use Dirthara\Validation\ValidationContext;
use Dirthara\Validation\Rule\SkipsMissing;
use Dirthara\Validation\Contract\AcceptsValue;

final class AllowEmptyString implements AcceptsValue
{
    use SkipsMissing;

    public function __construct(
        public readonly string $message = '{input} may be empty',
    ) {}

    public function accepts(mixed $value): bool
    {
        return $value === '';
    }

    public function validate(mixed $value, ValidationContext $context): array
    {
        return [];
    }
}
```
