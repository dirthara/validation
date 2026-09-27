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
use Dirthara\Validation\Rule\String\Email;
use Dirthara\Validation\Rule\Presence\Required;
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

A field that is missing from the input and a field whose value is `null` are different things.

A **missing** field reaches its rules as `Dirthara\Validation\Missing::Value`. Only the rules that implement
`Dirthara\Validation\Contract\ValidatesMissing` run for it, still in order, so a field is optional unless it has one
of them. `Required` and `Present` are two such rules.

**`null`** is an ordinary value, and every rule receives it. Rules that expect a certain type, such as `Email`, `Each`,
and `Nested`, reject it. Add `Nullable` to a field's rules to accept `null`: the field then passes for `null` without
running any of its other rules. Where `Nullable` stands in the list does not matter.

| Rules                                           | Missing                  | `null`                   |
|-------------------------------------------------|--------------------------|--------------------------|
| `new Email()`                                   | Passes                   | Fails (`Email`)          |
| `[new Nullable(), new Email()]`                 | Passes                   | Passes                   |
| `[new Required(), new Email()]`                 | Fails (`Required`)       | Fails (`Required`)       |
| `[new Present(), new Nullable(), new Email()]`  | Fails (`Present`)        | Passes                   |

Any other value is checked by `Email` in every row. The last row is a field that has to be sent, but may be `null`, such
as a value a client clears on purpose.

:::caution
`Nullable` takes precedence over `Required`: with both, `null` passes and only a missing field fails. Use `Present`
together with `Nullable` to say that, and `Required` alone when `null` is not allowed.
:::

## Rules run in order and stop at the first failure

The rules of a field run in the order they are listed, and the first rule that fails ends the field. A field therefore
reports the errors of at most one rule, although that one rule, such as `Each`, can report several.

This lets a rule rely on the rules before it. A rule that checks a string's length can come after a rule that checks the
value is a string, without having to handle every other type itself.

## Fields without rules

Input fields that have no rules are ignored, and they do not cause an error. The result reports only whether the input is
valid, not a filtered copy of it, so read the values you declared rules for from your own input.

## Input keys

The input is an array with any keys. Integer keys, which decoded JSON often has, are looked up like any other key.
