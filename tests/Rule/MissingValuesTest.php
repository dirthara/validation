<?php

declare(strict_types=1);

namespace Dirthara\Validation\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Dirthara\Validation\Contract\Rule;
use Dirthara\Validation\Rule\Text\Url;
use PHPUnit\Framework\Attributes\Test;
use Dirthara\Validation\Rule\Text\Uuid;
use Dirthara\Validation\Rule\Text\Email;
use Dirthara\Validation\Rule\Text\Regex;
use Dirthara\Validation\Rule\Text\Length;
use Dirthara\Validation\ValidatorFactory;
use Dirthara\Validation\Rule\Type\Numeric;
use Dirthara\Validation\Rule\Choice\Choice;
use Dirthara\Validation\Rule\Text\MaxLength;
use Dirthara\Validation\Rule\Text\MinLength;
use Dirthara\Validation\Rule\Type\ArrayType;
use Dirthara\Validation\Rule\Type\FloatType;
use Dirthara\Validation\Rule\Collection\Each;
use Dirthara\Validation\Rule\Comparison\Same;
use Dirthara\Validation\Rule\Numeric\Between;
use Dirthara\Validation\Rule\Numeric\Maximum;
use Dirthara\Validation\Rule\Numeric\Minimum;
use Dirthara\Validation\Rule\Type\StringType;
use Dirthara\Validation\Rule\Choice\NotChoice;
use Dirthara\Validation\Rule\Collection\Count;
use Dirthara\Validation\Rule\Numeric\Negative;
use Dirthara\Validation\Rule\Numeric\Positive;
use Dirthara\Validation\Rule\Presence\Present;
use Dirthara\Validation\Rule\Structure\Nested;
use Dirthara\Validation\Rule\Type\BooleanType;
use Dirthara\Validation\Rule\Type\IntegerType;
use PHPUnit\Framework\Attributes\DataProvider;
use Dirthara\Validation\Rule\Presence\Nullable;
use Dirthara\Validation\Rule\Presence\Required;
use Dirthara\Validation\Rule\Type\IterableType;
use Dirthara\Validation\Rule\Collection\MaxCount;
use Dirthara\Validation\Rule\Collection\MinCount;
use Dirthara\Validation\Rule\Presence\RequiredIf;
use Dirthara\Validation\Rule\Comparison\Different;
use Dirthara\Validation\Rule\Presence\RequiredUnless;

final class MissingValuesTest extends TestCase
{
    /**
     * @return iterable<string, array{Rule, bool}>
     */
    public static function rules(): iterable
    {
        yield 'Required' => [new Required(), true];
        yield 'Present' => [new Present(), true];
        yield 'RequiredIf' => [new RequiredIf('type', 'business'), true];
        yield 'RequiredUnless' => [new RequiredUnless('type', 'personal'), true];
        yield 'Nullable' => [new Nullable(), false];
        yield 'StringType' => [new StringType(), false];
        yield 'IntegerType' => [new IntegerType(), false];
        yield 'FloatType' => [new FloatType(), false];
        yield 'BooleanType' => [new BooleanType(), false];
        yield 'Numeric' => [new Numeric(), false];
        yield 'ArrayType' => [new ArrayType(), false];
        yield 'IterableType' => [new IterableType(), false];
        yield 'Email' => [new Email(), false];
        yield 'Length' => [new Length(3), false];
        yield 'MinLength' => [new MinLength(3), false];
        yield 'MaxLength' => [new MaxLength(3), false];
        yield 'Regex' => [new Regex('/^a$/'), false];
        yield 'Url' => [new Url(), false];
        yield 'Uuid' => [new Uuid(), false];
        yield 'Minimum' => [new Minimum(1), false];
        yield 'Maximum' => [new Maximum(1), false];
        yield 'Between' => [new Between(1, 2), false];
        yield 'Positive' => [new Positive(), false];
        yield 'Negative' => [new Negative(), false];
        yield 'Same' => [new Same('email'), false];
        yield 'Different' => [new Different('email'), false];
        yield 'Choice' => [new Choice(['a']), false];
        yield 'NotChoice' => [new NotChoice(['a']), false];
        yield 'Each' => [new Each(new Email()), false];
        yield 'Count' => [new Count(1), false];
        yield 'MinCount' => [new MinCount(1), false];
        yield 'MaxCount' => [new MaxCount(1), false];
        yield 'Nested' => [new Nested(new ValidatorFactory()->create([])), false];
    }

    #[Test]
    #[DataProvider('rules')]
    public function it_validates_a_missing_value_only_when_the_rule_decides_about_one(
        Rule $rule,
        bool $validatesMissing,
    ): void {
        self::assertSame($validatesMissing, $rule->validatesMissing);
    }
}
