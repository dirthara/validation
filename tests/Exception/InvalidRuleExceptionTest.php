<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Exception;

use RuntimeException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Dirthara\Validation\Exception\ValidationException;
use Dirthara\Validation\Exception\InvalidRuleException;

final class InvalidRuleExceptionTest extends TestCase
{
    #[Test]
    public function it_carries_nothing_by_default(): void
    {
        $exception = new InvalidRuleException();

        self::assertInstanceOf(ValidationException::class, $exception);
        self::assertInstanceOf(InvalidArgumentException::class, $exception);
        self::assertSame('', $exception->getMessage());
        self::assertSame(0, $exception->getCode());
        self::assertNull($exception->getPrevious());
        self::assertSame([], $exception->context);
    }

    #[Test]
    public function it_keeps_a_previous_exception_and_its_context(): void
    {
        $previous = new RuntimeException('cause');
        $exception = new InvalidRuleException('message', 3, $previous, ['rule' => 'required']);

        self::assertSame('message', $exception->getMessage());
        self::assertSame(3, $exception->getCode());
        self::assertSame($previous, $exception->getPrevious());
        self::assertSame(['rule' => 'required'], $exception->context);
    }

    /**
     * @return iterable<string, array{mixed, string}>
     */
    public static function invalidRules(): iterable
    {
        yield 'string' => ['required', 'string'];
        yield 'integer' => [42, 'integer'];
        yield 'null' => [null, 'NULL'];
        yield 'array' => [[], 'array'];
    }

    #[Test]
    #[DataProvider('invalidRules')]
    public function it_describes_an_invalid_rule_by_its_type(mixed $rule, string $type): void
    {
        $exception = InvalidRuleException::invalidRule($rule);

        self::assertSame('Rule "' . $type . '" is not a valid rule.', $exception->getMessage());
        self::assertSame(['rule' => $rule], $exception->context);
    }
}
