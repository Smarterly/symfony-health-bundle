<?php

declare(strict_types=1);

namespace Tests\Unit\QueryBus\ResultAssertion;

use Cushon\HealthBundle\Message\Result\HealthCheck;
use Cushon\HealthBundle\QueryBus\ResultAssertion\DefaultAssertion;
use Cushon\HealthBundle\QueryBus\ResultAssertion\Exception\ResultClassOrInterfaceNotFound;
use Cushon\HealthBundle\QueryBus\ResultAssertion\Exception\ResultTypeClassDoesNotMatchExpected;
use Cushon\HealthBundle\QueryBus\ResultAssertion\Exception\UnexpectedResultType;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use stdClass;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class DefaultAssertionTest extends TestCase
{
    use ProphecyTrait;

    public function testItThrowsAnExceptionIfTheResultTypeIsNotAClassOrInterface(): void
    {
        $incorrectClass = 'foo';
        $this->expectExceptionObject(ResultClassOrInterfaceNotFound::fromString($incorrectClass));
        new DefaultAssertion($incorrectClass);
    }

    public function testItThrowsAnExceptionIfTheClassOrInterfaceToCheckIsNotAHealthCheckResult(): void
    {
        $incorrectResultClass = __CLASS__;
        $this->expectExceptionObject(ResultTypeClassDoesNotMatchExpected::create(
            $incorrectResultClass
        ));

        new DefaultAssertion($incorrectResultClass);
    }

    public function testItAcceptsAValidResult(): void
    {
        $result = $this->prophesize(HealthCheck::class)->reveal();

        $assertion = new DefaultAssertion();
        $assertion->assertValidResult($result);
        $this->expectNotToPerformAssertions();
    }

    public function testItThrowsAnUnexpectedResultTypeExceptionForInvalidResult(): void
    {
        $result = new stdClass();

        $assertion = new DefaultAssertion();
        $this->expectExceptionObject(
            UnexpectedResultType::fromResult($result, HealthCheck::class)
        );
        $assertion->assertValidResult($result);
    }
}
