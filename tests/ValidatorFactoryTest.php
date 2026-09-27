<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Validator;
use Dirthara\Validation\Rule\Email;
use Dirthara\Validation\Rule\Nested;
use Dirthara\Validation\Rule\Required;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidatorFactory;
use Dirthara\Validation\Exception\InvalidRuleException;

final class ValidatorFactoryTest extends TestCase
{
    #[Test]
    public function it_creates_a_validator_from_rules_per_field(): void
    {
        $validator = new ValidatorFactory()->create([
            'name' => new Required(),
            'email' => [new Required(), new Email()],
            'address' => new Nested(new ValidatorFactory()->create(['email' => new Email()])),
        ]);

        self::assertInstanceOf(Validator::class, $validator);
        self::assertEquals(
            [
                new ValidationError(message: 'The value is required.', code: 'required', path: ['name']),
                new ValidationError(
                    message: 'The value must be a valid email address.',
                    code: 'email',
                    path: ['email'],
                ),
                new ValidationError(
                    message: 'The value must be a valid email address.',
                    code: 'email',
                    path: ['address', 'email'],
                ),
            ],
            $validator->validate(['email' => 'invalid', 'address' => ['email' => 'invalid']])->errors,
        );
    }

    #[Test]
    public function it_rejects_a_field_rule_that_is_not_a_rule(): void
    {
        try {
            // @mago-expect analysis:possibly-invalid-argument The invalid rule is the point of the test
            new ValidatorFactory()->create(['name' => new Email(), 'email' => [new Email(), 'required']]);
            self::fail('Expected an InvalidRuleException.');
        } catch (InvalidRuleException $exception) {
            self::assertSame('Rule "string" is not a valid rule.', $exception->getMessage());
            self::assertSame(['rule' => 'required', 'field' => 'email'], $exception->context);
        }
    }
}
