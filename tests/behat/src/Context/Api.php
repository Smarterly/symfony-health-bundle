<?php

declare(strict_types=1);

namespace Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
final class Api implements Context
{
    /**
     * @Given that all dependencies are healthy
     */
    public function thatAllDependenciesAreHealthy(): void
    {
        throw new PendingException();
    }

    /**
     * @When I ask what the health of the service is
     */
    public function iAskWhatTheHealthOfTheServiceIs(): void
    {
        throw new PendingException();
    }

    /**
     * @Then I am informed the service is healthy.
     */
    public function iAmInformedTheServiceIsHealthy(): void
    {
        throw new PendingException();
    }

    /**
     * @Given that one dependency is unhealthy
     */
    public function thatOneDependencyIsUnhealthy(): void
    {
        throw new PendingException();
    }

    /**
     * @Then I am informed the service is unhealthy.
     */
    public function iAmInformedTheServiceIsUnhealthy(): void
    {
        throw new PendingException();
    }
}
