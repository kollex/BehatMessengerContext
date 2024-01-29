<?php

declare(strict_types=1);

namespace BehatMessengerContext\Tests\Context\Traits;

use BehatMessengerContext\Context\Traits\ArraySimilarTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ArraySimilarTrait::class)]
final class ArraySimilarTraitTest extends TestCase
{
    use ArraySimilarTrait;

    private const ATOM_DATETIME_PATTERN =
        '\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2]\d|3[0-1])T[0-2]\d:[0-5]\d:[0-5]\d[+-][0-2]\d:[0-5]\d';

    public function testSuccess(): void
    {
        $result = $this->isArraysSimilar(['test'], ['test']);

        $this->assertTrue($result);
    }

    public function testEmptyArraySuccess(): void
    {
        $result = $this->isArraysSimilar([], []);

        $this->assertTrue($result);
    }

    public function testFail(): void
    {
        $result = $this->isArraysSimilar([], ['test']);

        $this->assertFalse($result);
    }

    public function testVariableFieldsSuccess(): void
    {
        $result = $this->isArraysSimilar(
            [
                'time' => '~' . self::ATOM_DATETIME_PATTERN,
                'foo' => 1,
            ],
            [
                'time' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
                'foo' => 1,
            ],
            ['time'],
        );

        $this->assertTrue($result);
    }

    /**
     * @var array<mixed>$expected
     * @var array<mixed>$actual
     * @var array<string>$variableFields
     * @var array<string,string>$actual
     */
    #[DataProvider('variableFieldsFailProvider')]
    public function testVariableFieldsFail(
        array $expected,
        array $actual,
        array $variableFields,
        array $placeholderPatternMap = []
    ): void {
        $result = $this->isArraysSimilar($expected, $actual, $variableFields, $placeholderPatternMap);

        $this->assertFalse($result);
    }

    public static function variableFieldsFailProvider(): iterable
    {
        yield '#1: Value type different from string' => [
            ['a' => 1],
            ['a' => 2],
            ['a'],
        ];

        yield '#2: Value without ~ prefix' => [
            ['a' => 'foo'],
            ['a' => 'bar'],
            ['a'],
        ];

        yield '#3: Do not match the pattern' => [
            ['a' => '~^t[a-z]'],
            ['a' => 'foo'],
            ['a'],
        ];

        yield '#4: Empty placeholder-pattern map' => [
            ['a' => '{test}'],
            ['a' => 'foo'],
            ['a'],
        ];

        yield '#5: Placeholder is not closed' => [
            ['a' => '{test'],
            ['a' => 'foo'],
            ['a'],
            ['test' => '/foo/'],
        ];
    }

    public function testVariableFieldsPlaceholder(): void
    {
        $result = $this->isArraysSimilar(
            ['date' => '{datetime_atom}'],
            ['date' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM)],
            ['date'],
            ['datetime_atom' => '/' . self::ATOM_DATETIME_PATTERN . '/'],
        );

        $this->assertTrue($result);
    }
}
