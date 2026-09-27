---
id: results-and-errors
title: Results and errors
sidebar_position: 5
description: What a validation returns, and what each error carries.
---

## The result

`validate()` returns a `ValidationResult`.

| Member             | Type                                        | Meaning                                         |
|--------------------|---------------------------------------------|-------------------------------------------------|
| `errors`           | `list<ValidationError>`                     | Every error, in the order of the fields.        |
| `valid()`          | `bool`                                      | Whether there are no errors.                    |
| `failed()`         | `bool`                                      | Whether there is at least one error.            |
| `errorsByField()`  | `array<array-key, list<ValidationError>>`   | The errors, grouped by their `field`.           |

## Errors

Every `ValidationError` carries:

| Property     | Type                    | Meaning                                                                        |
|--------------|-------------------------|--------------------------------------------------------------------------------|
| `messageKey` | `string`                | The rule's message with its placeholders, such as `{input} is required`.       |
| `message`    | `string`                | The message key with every placeholder filled in, such as `email is required`. |
| `parameters` | `array<string, mixed>`  | The values for the placeholders other than `{input}`, such as a minimum.       |
| `path`       | `list<string\|int>`     | The keys from the input's root to the value, such as `['users', 0, 'email']`.  |
| `field`      | `string`                | The path joined with dots, such as `users.0.email`.                            |

`message` and `field` are worked out from the other properties each time you read them.

Use `path` when you need to find the value again: `field` is ambiguous when a key itself contains a dot, but `path`
keeps every key intact.

## Messages and translation

A message key is readable English, so `message` is a meaningful message without any translation. `{input}` becomes the
field, or `input` for an error without a path, and every other placeholder becomes the parameter of the same name. A
parameter named `input` replaces the field. A placeholder without a parameter stays as it is.

```php
$error->messageKey;   // '{input} must be at least {minimum}'
$error->parameters;   // ['minimum' => 18]
$error->field;        // 'age'
$error->message;      // 'age must be at least 18'
```

The package does not translate. To show messages in another language, look the message key up in your own catalogue,
which uses the English message as its key, and fill in the placeholders yourself:

```php
$catalogue = [
    '{input} is required' => '{input} is verplicht',
    '{input} must be a valid email address' => '{input} moet een geldig e-mailadres zijn',
];

$translated = strtr($catalogue[$error->messageKey] ?? $error->messageKey, [
    '{input}' => $error->field,
    // one entry for each of $error->parameters
]);
```

:::caution
The message key is the translation key. Changing the wording of a rule's message, including a custom `message`
argument, changes the key, so update your catalogues with it.
:::
