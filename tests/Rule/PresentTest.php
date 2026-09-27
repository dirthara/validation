<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Missing;
use Dirthara\Validation\Rule\Present;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\ValidationError;
use Dirthara\Validation\Contract\ValidatesMissing;

final class PresentTest extends TestCase
{
    #[Test]
    public function it_validates_a_missing_value(): void
    {
        self::assertInstanceOf(ValidatesMissing::class, new Present());
    }

    #[Test]
    public function it_accepts_any_value_that_is_present_including_null(): void
    {
        self::assertSame([], new Present()->validate('value'));
        self::assertSame([], new Present()->validate(null));
        self::assertSame([], new Present()->validate(''));
    }

    #[Test]
    public function it_rejects_a_missing_value(): void
    {
        self::assertEquals(
            [new ValidationError(message: 'The value must be present.', code: 'present')],
            new Present()->validate(Missing::Value),
        );
    }
}
