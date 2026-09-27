---
id: intro
title: Dirthara Validation
sidebar_position: 1
description: Validate input arrays against rules per field, with nested input and structured errors.
---

Dirthara Validation checks an input array against rules per field and returns every error with the path of the field
that caused it. Rules are small immutable objects, and nested input is validated by composing rules rather than by
writing dotted field names.

```php
use Dirthara\Validation\Rule\Collection\Each;
use Dirthara\Validation\Rule\String\Email;
use Dirthara\Validation\Rule\Structure\Nested;
use Dirthara\Validation\Rule\Presence\Required;
use Dirthara\Validation\ValidatorFactory;

$factory = new ValidatorFactory();

$validator = $factory->create([
    'email' => [new Required(), new Email()],
    'contacts' => new Each(new Nested($factory->create([
        'email' => [new Required(), new Email()],
    ]))),
]);

$result = $validator->validate([
    'email' => 'ada@example.com',
    'contacts' => [['email' => 'grace@example.com'], ['email' => 'invalid']],
]);

$result->valid();              // false
$result->errors[0]->field;     // 'contacts.1.email'
$result->errors[0]->message;   // 'contacts.1.email must be a valid email address'
```

- [Validating input](validating-input.md) covers the factory, rules per field, and how missing values are handled.
- [Rules](rules.md) lists the rules the package ships and how to write your own.
- [Results and errors](results-and-errors.md) describes what a validation returns.
- [Error handling](error-handling.md) lists the exceptions the package throws.

See [installation](installation.md) for requirements and development setup.
