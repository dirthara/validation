<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Missing;
use Dirthara\Validation\Rule\Required;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Contract\ValidatesMissing;

final class RequiredTest extends TestCase
{
    #[Test]
    public function it_validates_a_missing_value(): void
    {
        self::assertInstanceOf(ValidatesMissing::class, new Required());
    }

    #[Test]
    public function it_accepts_any_value_but_null(): void
    {
        self::assertSame([], new Required()->validate('value'));
        self::assertSame([], new Required()->validate(''));
        self::assertSame([], new Required()->validate(0));
        self::assertSame([], new Required()->validate(false));
    }

    #[Test]
    public function it_rejects_a_missing_value_and_null(): void
    {
        $required = [new ValidationError(message: 'The value is required.', code: 'required')];

        self::assertEquals($required, new Required()->validate(Missing::Value));
        self::assertEquals($required, new Required()->validate(null));
    }
}
