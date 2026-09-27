---
id: validating-input
title: Validating input
sidebar_position: 3
description: Create a validator, give each field its rules, and understand how missing values and failures are handled.
---

## Create a validator

`ValidatorFactory::create()` is the only way to create a validator. It takes an array that maps each field to a rule or
a list of rules, and returns a `Dirthara\Validation\Contract\Validator`.

```php
use Dirthara\Validation\Rule\Email;
use Dirthara\Validation\Rule\Required;
use Dirthara\Validation\ValidatorFactory;

$validator = new ValidatorFactory()->create([
    'name' => new Required(),
    'email' => [new Required(), new Email()],
]);

$result = $validator->validate($input);
```

The validator is immutable, so create it once and validate as many inputs with it as you need.

An entry that is not a rule, such as a string or a validator that is not wrapped in a `Nested` rule, throws an
`InvalidRuleException` from `create()`. Its context names the field. See [error handling](error-handling.md).

## Missing and null values

A field that is missing from the input and a field whose value is `null` are treated the same way:

| The field has                    | A missing or `null` value                      |
|----------------------------------|------------------------------------------------|
| A `Required` rule                | Fails with the `required` error.               |
| No `Required` rule               | Passes. None of its other rules run.           |

Every other rule only ever sees a value that is present and not `null`, so a rule never has to check for one. The same
applies to the items of an `Each` rule and the fields of a `Nested` rule.

## Rules run in order and stop at the first failure

The rules of a field run in the order they are listed, and the first rule that fails ends the field. A field therefore
reports the errors of at most one rule, although that one rule, such as `Each`, can report several.

This lets a rule rely on the rules before it. A rule that checks a string's length can come after a rule that checks the
value is a string, without having to handle every other type itself.

## Fields without rules

Input fields that have no rules are ignored. They do not cause an error, and they are not part of the
[validated input](results-and-errors.md#validated-input).

## Input keys

The input is an array with any keys. Integer keys, which decoded JSON often has, are looked up like any other key.
