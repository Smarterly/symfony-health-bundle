<?php

declare(strict_types=1);

namespace Tests\Unit\QueryBus\Exception;

use Cushon\HealthBundle\QueryBus\ResultAssertion\Exception\UnexpectedResultType;
use PHPUnit\Framework\TestCase;
use stdClass;

final class UnexpectedResultTypeTest extends TestCase
{
    public function testItHasTheResult(): void
    {
        $incorrectResult = new stdClass();
        $expected = __CLASS__;

        $exception = UnexpectedResultType::fromResult($incorrectResult, $expected);

        $this->assertSame($incorrectResult, $exception->getResult());
        $this->assertStringContainsString(__CLASS__, $exception->getMessage());
    }

    public function testItHasTheExpectedt(): void
    {
        $incorrectResult = new stdClass();
        $expected = __CLASS__;

        $exception = UnexpectedResultType::fromResult($incorrectResult, $expected);

        $this->assertSame($expected, $exception->getExpected());
        $this->assertStringContainsString(stdClass::class, $exception->getMessage());
    }

    public function testItAddsTheIncorrectTypeToTheMessage(): void
    {
        $incorrectResult = [];
        $expected = __CLASS__;

        $exception = UnexpectedResultType::fromResult($incorrectResult, $expected);

        $this->assertSame($expected, $exception->getExpected());
        $this->assertStringContainsString('array', $exception->getMessage());
    }
}
