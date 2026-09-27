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
| `errorsByField()`  | `array<string, list<ValidationError>>`      | The errors, grouped by their `field`.           |

## Errors

Every `ValidationError` carries:

| Property     | Type                    | Meaning                                                                    |
|--------------|-------------------------|----------------------------------------------------------------------------|
| `message`    | `string`                | A description in English, meant for developers and as a default.          |
| `code`       | `string`                | A stable identifier of the failure, such as `required` or `email`.        |
| `parameters` | `array<string, mixed>`  | The values a translated message needs, such as a minimum length.          |
| `path`       | `list<string\|int>`     | The keys from the input's root to the value, such as `['users', 0, 'email']`. |
| `field`      | `string`                | The path joined with dots, such as `users.0.email`.                        |

Use `code` and `parameters` to build messages in another language, and `path` when you need to find the value again:
`field` is ambiguous when a key itself contains a dot, but `path` keeps every key intact.
