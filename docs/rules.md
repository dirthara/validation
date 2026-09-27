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
| `Email`                      | The value is a string that is a valid email address.     | `email`    |
| `Each(Rule\|list<Rule>)`     | The value is iterable and every item passes the rules.   | `iterable` |
| `Nested(Validator)`          | The value is an array that passes the nested validator.  | `array`    |

The error code in the table is the one the rule itself reports. `Each` and `Nested` also pass on the errors of the
rules they contain.

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
passes. It never receives a missing or `null` value, so it does not need to check for one.

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
