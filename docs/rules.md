---
id: rules
title: Rules
sidebar_position: 4
description: The rules Dirthara Validation ships, and how to write your own.
---

## Available rules

All rules live in `Dirthara\Validation\Rule`.

| Rule                         | Passes when                                              | Error code |
|------------------------------|----------------------------------------------------------|------------|
| `Required`                   | The value is present and not `null`.                     | `required` |
| `Present`                    | The value is present, even when it is `null`.            | `present`  |
| `Nullable`                   | Never fails. Makes the field pass for `null`.            |            |
| `Email`                      | The value is a string that is a valid email address.     | `email`    |
| `Each(Rule\|list<Rule>)`     | The value is iterable and every item passes the rules.   | `iterable` |
| `Nested(Validator)`          | The value is an array that passes the nested validator.  | `array`    |

The error code in the table is the one the rule itself reports. `Each` and `Nested` also pass on the errors of the rules
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

```php
use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\ValidationError;

final readonly class MinLength implements Rule
{
    public function __construct(
        private int $min,
    ) {}

    public function validate(mixed $value): array
    {
        if (is_string($value) && mb_strlen($value) >= $this->min) {
            return [];
        }

        return [new ValidationError(
            message: sprintf('The value must be at least %d characters.', $this->min),
            code: 'min_length',
            parameters: ['min' => $this->min],
        )];
    }
}
```

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
    ) {}

    public function validate(mixed $value): array
    {
        if (!$this->condition || !$value instanceof Missing) {
            return [];
        }

        return [new ValidationError(message: 'The value is required.', code: 'required')];
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
