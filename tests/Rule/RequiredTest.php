<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Rule\Required;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Contract\ValidatesNull;

final class RequiredTest extends TestCase
{
    #[Test]
    public function it_validates_null(): void
    {
        self::assertInstanceOf(ValidatesNull::class, new Required());
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
    public function it_rejects_null(): void
    {
        self::assertEquals(
            [new ValidationError(message: 'The value is required.', code: 'required')],
            new Required()->validate(null),
        );
    }
}
