<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\QueryBus;

use Cushon\HealthBundle\Message\Query\HealthCheck;
use Cushon\HealthBundle\Message\Query\HealthCheck as HealthCheckQuery;
use Cushon\HealthBundle\Message\Result\HealthCheck as HealthCheckResult;
use Cushon\HealthBundle\Message\Result\HealthCheck\Healthy;
use Cushon\HealthBundle\QueryBus\Exception\ResultAssertionFailed;
use Cushon\HealthBundle\QueryBus\Exception\SymfonyMessengerError;
use Cushon\HealthBundle\QueryBus\ResultAssertion\Exception\AssertionError;
use Cushon\HealthBundle\QueryBus\ResultAssertion\Exception\ResultClassOrInterfaceNotFound;
use Cushon\HealthBundle\QueryBus\ResultAssertion\Exception\ResultTypeClassDoesNotMatchExpected;
use Cushon\HealthBundle\QueryBus\ResultAssertion\Exception\UnexpectedResultType;
use ReflectionClass;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerHealthQueryBus implements HealthCheckQueryBus
{
    use HandleTrait;

    private ResultAssertion $resultAssertion;

    /**
     * @param MessageBusInterface $messageBus
     */
    public function __construct(
        MessageBusInterface $messageBus,
        ResultAssertion $resultAssertion
    ) {
        $this->messageBus = $messageBus;
        $this->resultAssertion = $resultAssertion;
    }

    /**
     * @inheritDoc
     */
    public function handleHealthCheckQuery(HealthCheckQuery $healthCheckQuery): HealthCheckResult
    {
        try {
            /** @var HealthCheckResult $result */
            $result = $this->handle($healthCheckQuery);
            $this->resultAssertion->assertValidResult($result);
        } catch (ExceptionInterface $exc) {
            throw SymfonyMessengerError::fromMessengerException($exc);
        } catch (AssertionError $exc) {
            throw ResultAssertionFailed::fromAssertionException($exc);
        }

        return $result;
    }
}
