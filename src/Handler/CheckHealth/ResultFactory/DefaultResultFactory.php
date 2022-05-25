<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Handler\CheckHealth\ResultFactory;

use Cushon\HealthBundle\ApplicationHealth\HealthReport;
use Cushon\HealthBundle\ApplicationHealth\HealthReport\DependencyStatus;
use Cushon\HealthBundle\Handler\CheckHealth\ResultFactory;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use Cushon\HealthBundle\Message\Result\HealthCheck\Healthy;
use Cushon\HealthBundle\Message\Result\HealthCheck\Unhealthy;

final class DefaultResultFactory implements ResultFactory
{
    /**
     * @inheritDoc
     */
    public function fromHealth(HealthReport $health): HealthCheck
    {
        /** @var DependencyStatus[] $dependencies */
        $dependencies = $health->dependencies();

        if ($health->isHealthy()) {
            return $this->createHealthy(...$dependencies);
        }

        return $this->createUnhealthy(...$dependencies);
    }

    /**
     * @param DependencyStatus ...$dependencies
     * @return Healthy
     */
    private function createHealthy(DependencyStatus ...$dependencies): Healthy
    {
        return new Healthy(...$dependencies);
    }

    /**
     * @param DependencyStatus ...$dependencies
     * @return Unhealthy
     */
    private function createUnhealthy(DependencyStatus ...$dependencies): Unhealthy
    {
        return new Unhealthy(...$dependencies);
    }
}
