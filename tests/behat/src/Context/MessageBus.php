<?php

declare(strict_types=1);

namespace Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Cushon\HealthBundle\ApplicationHealth\DependencyCheck;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus\SimpleStatus;
use Cushon\HealthBundle\Message\Query\HealthCheck\DefaultHealthCheckQuery;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use Cushon\HealthBundle\QueryBus\MessengerHealthQueryBus;
use PHPUnit\Framework\Assert;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\Behat\Context\Traits\ProphecyContextTrait;

final class MessageBus implements Context
{
    use ProphecyContextTrait;

    /** @var KernelInterface  */
    private KernelInterface $kernel;

    /** @var HealthCheck|null  */
    private ?HealthCheck $result;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given that all dependencies are healthy
     */
    public function thatAllDependenciesAreHealthy(): void
    {
        $healthyStatus1 = new SimpleStatus('Healthy service', true);
        $healthyStatus2 = new SimpleStatus('Healthy service', true);
        $this->addSyntheticDependencies($healthyStatus1, $healthyStatus2);
    }

    /**
     * @When I ask what the health of the service is
     */
    public function iAskWhatTheHealthOfTheServiceIs(): void
    {
        $this->kernel->boot();
        /** @var ContainerBuilder $container */
        $container = $this->kernel->getContainer();

        /** @var MessengerHealthQueryBus $queryBus */
        $queryBus = $container->get(MessengerHealthQueryBus::class);

        $this->result = $queryBus->handleHealthCheckQuery(new DefaultHealthCheckQuery());
    }

    /**
     * @Then I am informed the service is healthy.
     */
    public function iAmInformedTheServiceIsHealthy(): void
    {
        Assert::assertTrue($this->result->isHealthy());
    }

    /**
     * @Given that one dependency is unhealthy
     */
    public function thatOneDependencyIsUnhealthy(): void
    {
        $healthyStatus1 = new SimpleStatus('Unhealthy service', false);
        $healthyStatus2 = new SimpleStatus('Healthy service', true);
        $this->addSyntheticDependencies($healthyStatus1, $healthyStatus2);
    }

    /**
     * @Then I am informed the service is unhealthy.
     */
    public function iAmInformedTheServiceIsUnhealthy(): void
    {
        Assert::assertFalse($this->result->isHealthy());
    }

    private function addSyntheticDependencies(
        SimpleStatus $simpleStatus1,
        SimpleStatus $simpleStatus2
    ): void {
        /** @var ContainerBuilder $container */
        $container = $this->kernel->getContainer();

        $dependencyCheck1 = $this->prophesize(DependencyCheck::class);
        $dependencyCheck1->check()->willReturn($simpleStatus1);
        $dependencyCheck2 = $this->prophesize(DependencyCheck::class);
        $dependencyCheck2->check()->willReturn($simpleStatus2);

        $container->set('app.dependency_check1', $dependencyCheck1->reveal());
        $container->set('app.dependency_check2', $dependencyCheck2->reveal());
    }
}
