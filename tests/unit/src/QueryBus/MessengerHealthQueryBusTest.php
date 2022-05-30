<?php

declare(strict_types=1);

namespace Tests\Unit\QueryBus;

use Cushon\HealthBundle\Message\Query\HealthCheck\DefaultHealthCheckQuery;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use Cushon\HealthBundle\Message\Result\HealthCheck\Healthy;
use Cushon\HealthBundle\QueryBus\Exception\ResultAssertionFailed;
use Cushon\HealthBundle\QueryBus\Exception\SymfonyMessengerError;
use Cushon\HealthBundle\QueryBus\MessengerHealthQueryBus;
use Cushon\HealthBundle\QueryBus\ResultAssertion;
use Cushon\HealthBundle\QueryBus\ResultAssertion\Exception\UnexpectedResultType;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use StdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class MessengerHealthQueryBusTest extends TestCase
{
    use ProphecyTrait;

    public function testItHandlesAHealthCheckQuery(): void
    {
        $messageBus = $this->prophesize(MessageBusInterface::class);
        $resultAssertion = $this->prophesize(ResultAssertion::class);
        $healthCheckQuery = new DefaultHealthCheckQuery();
        $healthCheckResult = new Healthy();

        $envelope = new Envelope(
            $healthCheckResult,
            [new HandledStamp($healthCheckResult, 'foo')]
        );

        $messageBus->dispatch(new DefaultHealthCheckQuery())->willReturn($envelope);

        $healthQueryBus = new MessengerHealthQueryBus(
            $messageBus->reveal(),
            $resultAssertion->reveal()
        );

        $this->assertSame(
            $healthCheckResult,
            $healthQueryBus->handleHealthCheckQuery($healthCheckQuery)
        );

        $resultAssertion->assertValidResult($healthCheckResult)->shouldHaveBeenCalled();
    }

    public function testItThrowsAnExceptionIfTheResultIsNotAHealthCheckResult(): void
    {
        $messageBus = $this->prophesize(MessageBusInterface::class);

        $healthCheckQuery = new DefaultHealthCheckQuery();
        $incorrectResult = new StdClass();

        $envelope = new Envelope(
            $incorrectResult,
            [new HandledStamp($incorrectResult, 'foo')]
        );

        $resultAssertion = $this->prophesize(ResultAssertion::class);
        $assertExc = UnexpectedResultType::fromResult($incorrectResult, HealthCheck::class);
        $resultAssertion->assertValidResult($incorrectResult)->willThrow(
            $assertExc
        );
        $messageBus->dispatch($healthCheckQuery)->willReturn($envelope);
        $healthQueryBus = new MessengerHealthQueryBus(
            $messageBus->reveal(),
            $resultAssertion->reveal()
        );

        $this->expectExceptionObject(ResultAssertionFailed::fromAssertionException($assertExc));

        $healthQueryBus->handleHealthCheckQuery($healthCheckQuery);
    }

    public function testItThrowsASymfonyMessengerErrorIfTheMessageBusThrowsAnException(): void
    {
        $healthCheckQuery = new DefaultHealthCheckQuery();
        $messageBus = $this->prophesize(MessageBusInterface::class);
        $resultAssertion = $this->prophesize(ResultAssertion::class);
        $messageBusException = new LogicException('foo');
        $messageBus->dispatch($healthCheckQuery)->willThrow($messageBusException);

        $healthQueryBus = new MessengerHealthQueryBus(
            $messageBus->reveal(),
            $resultAssertion->reveal()
        );
        $this->expectExceptionObject(SymfonyMessengerError::fromMessengerException($messageBusException));

        $healthQueryBus->handleHealthCheckQuery($healthCheckQuery);
    }
}
