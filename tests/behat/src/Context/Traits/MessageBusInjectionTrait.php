<?php

declare(strict_types=1);

namespace Tests\Behat\Context\Traits;

use Cushon\HealthBundle\Message\Query\HealthCheck as HealthCheckQuery;
use Cushon\HealthBundle\Message\Result\HealthCheck as HealthCheckResult;
use Cushon\HealthBundle\QueryBus\HealthCheckQueryBus;
use Cushon\HealthBundle\QueryBus\MessengerHealthQueryBus;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\KernelInterface;

trait MessageBusInjectionTrait
{
    private KernelInterface $kernel;
    private HealthCheckResult $healthCheck;

    /**
     * @return void
     */
    private function injectMessageBusMock(): void
    {
        /** @var ContainerBuilder $container */
        $container = $this->kernel->getContainer();

        $queryBus = $this->prophesize(HealthCheckQueryBus::class);
        $queryBus->handleHealthCheckQuery(
            Argument::type(HealthCheckQuery::class)
        )->willReturn($this->healthCheck);

        $container->set(MessengerHealthQueryBus::class, $queryBus->reveal());
    }

    /**
     * @param string $classOrInterface
     * @return ObjectProphecy
     */
    abstract private function prophesize(string $classOrInterface): ObjectProphecy;
}
