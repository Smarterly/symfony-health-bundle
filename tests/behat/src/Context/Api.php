<?php

declare(strict_types=1);

namespace Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus\SimpleStatus;
use Cushon\HealthBundle\Message\Result\HealthCheck\Healthy;
use Cushon\HealthBundle\Message\Result\HealthCheck\Unhealthy;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\Behat\Context\Traits\MessageBusInjectionTrait;
use Tests\Behat\Context\Traits\ProphecyContextTrait;

use function Safe\json_decode;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class Api implements Context
{
    use ProphecyContextTrait;
    use MessageBusInjectionTrait;

    private Response $response;

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
        $this->kernel->boot();
        $this->response = $this->kernel->handle(Request::create('/health'));
    }

    /**
     * @Then I am informed the service is healthy.
     */
    public function iAmInformedTheServiceIsHealthy(): void
    {
        $health = json_decode($this->response->getContent());
        Assert::assertSame('healthy', $health->status);
    }

    /**
     * @Then I am informed the service is unhealthy.
     */
    public function iAmInformedTheServiceIsUnhealthy(): void
    {
        $health = json_decode($this->response->getContent());
        Assert::assertSame('unhealthy', $health->status);
    }

    /**
     * @Given that one dependency is unhealthy
     */
    public function thatOneDependencyIsUnhealthy(): void
    {
        $this->healthCheck = new Unhealthy(
            new SimpleStatus('Test dependency 1', false, 'Degraded'),
            new SimpleStatus('Test dependency 2', true, 'All good'),
        );
    }
}
