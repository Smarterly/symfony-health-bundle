<?php

declare(strict_types=1);

namespace Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus\SimpleStatus;
use Cushon\HealthBundle\Console\Command\CheckHealth;
use Cushon\HealthBundle\Message\Query\HealthCheck;
use Cushon\HealthBundle\Message\Result\HealthCheck as HealthCheckResult;
use Cushon\HealthBundle\Message\Result\HealthCheck\Healthy;
use Cushon\HealthBundle\Message\Result\HealthCheck\Unhealthy;
use Cushon\HealthBundle\QueryBus\HealthCheckQueryBus;
use Cushon\HealthBundle\QueryBus\MessengerHealthQueryBus;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\Behat\Context\Traits\ProphecyContextTrait;

final class Console implements Context
{
    use ProphecyContextTrait;

    /** @var KernelInterface  */
    private KernelInterface $kernel;

    private HealthCheckResult $healthCheck;
    private Application $application;
    private OutputInterface $output;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->output = new BufferedOutput();
    }

    /**
     * @Given that all dependencies are healthy
     */
    public function thatAllDependenciesAreHealthy(): void
    {
        $this->healthCheck = new Healthy(
            new SimpleStatus('Test dependency 1', true),
            new SimpleStatus('Test dependency 2', true),
        );
    }

    /**
     * @When I ask what the health of the service is
     */
    public function iAskWhatTheHealthOfTheServiceIs(): void
    {
        $this->injectMessageBusMock();
        $application = new Application($this->kernel);
        $application->doRun(
            new ArgvInput([
            'console',
            CheckHealth::getDefaultName()
            ]),
            $this->output
        );
    }

    /**
     * @Then I am informed the service is healthy.
     */
    public function iAmInformedTheServiceIsHealthy(): void
    {
        $display = $this->output->fetch();
        Assert::assertStringContainsString('[OK] Status: Healthy', $display);
        Assert::assertMatchesRegularExpression('/Test dependency 1\s+Healthy/', $display);
    }

    /**
     * @Given that one dependency is unhealthy
     */
    public function thatOneDependencyIsUnhealthy(): void
    {
        $this->healthCheck = new Unhealthy(
            new SimpleStatus('Test dependency 1', false),
            new SimpleStatus('Test dependency 2', true),
        );
    }

    /**
     * @Then I am informed the service is unhealthy.
     */
    public function iAmInformedTheServiceIsUnhealthy(): void
    {
        $display = $this->output->fetch();
        Assert::assertStringContainsString('[WARNING] Status: Unhealthy!', $display);
        Assert::assertMatchesRegularExpression('/Test dependency 1\s+Unhealthy/', $display);
    }

    private function injectMessageBusMock(): void
    {
        /** @var ContainerBuilder $container */
        $container = $this->kernel->getContainer();

        $queryBus = $this->prophesize(HealthCheckQueryBus::class);
        $queryBus->handleHealthCheckQuery(
            Argument::type(HealthCheck::class)
        )->willReturn($this->healthCheck);

        $container->set(MessengerHealthQueryBus::class, $queryBus->reveal());
    }
}
