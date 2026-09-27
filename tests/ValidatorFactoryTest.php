<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Validator;
use Dirthara\Validation\Rule\Email;
use Dirthara\Validation\Rule\Nested;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\ValidatorFactory;
use Dirthara\Validation\Tests\Fixtures\RequiredRule;
use Dirthara\Validation\Exception\InvalidRuleException;

final class ValidatorFactoryTest extends TestCase
{
    #[Test]
    public function it_creates_a_validator_from_rules_per_field(): void
    {
        $validator = new ValidatorFactory()->create([
            'name' => new RequiredRule(),
            'email' => [new RequiredRule(), new Email()],
            'address' => new Nested(new ValidatorFactory()->create(['email' => new Email()])),
        ]);

        self::assertInstanceOf(Validator::class, $validator);
        self::assertEquals(
            [
                new ValidationError(field: 'name', message: 'The value is required.', code: 'required'),
                new ValidationError(field: 'email', message: 'The value must be a valid email address.', code: 'email'),
                new ValidationError(
                    field: 'address.email',
                    message: 'The value must be a valid email address.',
                    code: 'email',
                ),
            ],
            $validator->validate(['email' => 'invalid', 'address' => ['email' => 'invalid']])->errors,
        );
    }

    #[Test]
    public function it_rejects_a_field_rule_that_is_not_a_rule(): void
    {
        try {
            // @mago-expect analysis:possibly-invalid-argument -- the invalid rule is the point of the test
            new ValidatorFactory()->create(['name' => new Email(), 'email' => [new Email(), 'required']]);
            self::fail('Expected an InvalidRuleException.');
        } catch (InvalidRuleException $exception) {
            self::assertSame('Rule "string" is not a valid rule.', $exception->getMessage());
            self::assertSame(['rule' => 'required', 'field' => 'email'], $exception->context);
        }
    }
}
