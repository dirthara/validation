---
id: error-handling
title: Error handling
sidebar_position: 6
description: The exceptions Dirthara Validation throws, and what their context contains.
---

Every exception the package throws implements `Dirthara\Validation\Exception\ValidationException`. Catch that interface
to handle anything from the package, or a specific exception to handle one failure. Each exception carries a `context`
array with the details of the failure, and `addContext()` adds to it.

| Exception                    | Extends                     | Thrown when                                              |
|------------------------------|-----------------------------|----------------------------------------------------------|
| `InvalidRuleException`       | `InvalidArgumentException`  | A validator is created with something that is not a rule. |
| `ValidationFailedException`  | `RuntimeException`          | You throw it for a failed result.                        |

## Invalid rules

`ValidatorFactory::create()` and `Each` throw `InvalidRuleException` for an entry that is not a rule. Its context holds
the entry as `rule`, and the factory adds the field as `field`.

```php
try {
    $factory->create(['email' => [new Email(), 'required']]);
} catch (InvalidRuleException $exception) {
    $exception->getMessage();   // 'Rule "string" is not a valid rule.'
    $exception->context;        // ['rule' => 'required', 'field' => 'email']
}
```

## Failed validation

Validation itself never throws for invalid input; it returns a result. When you would rather stop with an exception,
create one from the result:

```php
$result = $validator->validate($input);

if ($result->failed()) {
    throw ValidationFailedException::forResult($result);
}
```

The exception keeps the whole result in `result`. Its message and its `fields` context name the fields that failed.

:::caution
The message and the context never contain the input values, because an exception ends up in logs and error trackers.
Read the values from your input, not from the exception.
:::
